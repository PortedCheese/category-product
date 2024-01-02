<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use PortedCheese\CategoryProduct\Facades\ProductFavorite;

class ProductFavoriteManager
{
    const COOKIE_NAME = "user-product-favorite";

    /**
     * Добавить в избранное.
     *
     * @param Product $product
     * @return array|string|null
     */
    public function addToFavorite(Product $product)
    {
        $id = $product->id;
        $favorite = $this->findCurrentFavorite();
        if (! in_array($id, $favorite)) {
            $favorite[] = $id;
            $result = $this->saveFavorite($favorite);
        }
        else {
            $result = $favorite;
        }
        return array_values($result);
    }

    /**
     * Удалить из збранного.
     *
     * @param Product $product
     * @return array|string|null
     */
    public function removeFromFavorite(Product $product)
    {
        $id = $product->id;
        $favorite = $this->findCurrentFavorite();
        if (($key = array_search($id, $favorite)) !== false) {
            unset($favorite[$key]);
            $result = $this->saveFavorite($favorite);
        }
        else {
            $result = $favorite;
        }
        return array_values($result);
    }

    /**
     * Очищаем избранное от товаров, которые были удалены
     *
     * @return array
     */
    public function getActualFavorite():array
    {
        $favorite = $this->findCurrentFavorite();
        $actualFavorite = [];
        foreach ($favorite as $id) {
            try {
                $find = Product::query()
                    ->select("id")
                    ->where("id","=",intval($id))
                    ->firstOrFail();
                $actualFavorite[] = $find->id;
            }
            catch (\Exception $e){
                $this->removeFromFavoriteById($id);
            }
        }
        return $actualFavorite;
    }

    /**
     * Удалить из збранного по ID (удаленный товар).
     *
     * @param $id
     * @return array|string|null
     */
    protected function removeFromFavoriteById($id)
    {
        $favorite = $this->findCurrentFavorite();
        if (($key = array_search($id, $favorite)) !== false) {
            unset($favorite[$key]);
            $result = $this->saveFavorite($favorite);
        }
        else {
            $result = $favorite;
        }
        return array_values($result);
    }

    /**
     * Получить избранное.
     *
     * @return array|mixed|string|null
     */
    public function getCurrentFavorite()
    {
        return $this->findCurrentFavorite();
    }

    /**
     * Сохранить.
     *
     * @param array $current
     * @return array
     */
    protected function saveFavorite(array $current)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->product_favorite = json_encode(array_values($current));
            $user->save();
        }

        $cookie = Cookie::make(self::COOKIE_NAME, json_encode(array_values($current)), 60*24*30);
        Cookie::queue($cookie);
        return $current;
    }

    /**
     * Найти избранное.
     *
     * @return array|string|null
     */
    protected function findCurrentFavorite()
    {
        $favorite = Cookie::get(self::COOKIE_NAME, "[]");
        if (! isset($favorite)) $favorite = "[]";
        $favorite = array_values(json_decode($favorite));
        return $this->checkUserFavorite($favorite);
    }

    /**
     * Если записанное отличается от куки, сохранить.
     *
     * @param $current
     * @return array
     */
    protected function checkUserFavorite($current)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (! empty($user->product_favorite)) {
                $userData = json_decode($user->product_favorite);
                if ($this->checkDiffInArrays($userData, $current)) {
                    $current = $this->mergeFavorite($current, $userData);
                    $current = $this->saveFavorite($current);
                }
            }
        }
        return array_values($current);
    }

    /**
     * Отличия в массивах.
     *
     * @param $first
     * @param $second
     * @return bool
     */
    protected function checkDiffInArrays($first, $second)
    {
        foreach ($first as $item) {
            if (! in_array($item, $second)) return true;
        }
        foreach ($second as $item) {
            if (! in_array($item, $first)) return true;
        }
        return false;
    }

    /**
     * Объеденить избранное.
     *
     * @param $current
     * @param $new
     * @return array
     */
    protected function mergeFavorite($current, $new)
    {
        return array_unique(array_merge($current, $new));
    }
}