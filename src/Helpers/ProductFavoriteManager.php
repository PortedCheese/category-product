<?php


namespace PortedCheese\CategoryProduct\Helpers;


use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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
        $cookie = Cookie::make(self::COOKIE_NAME, json_encode(array_values($current)), 60*24*30);
        Cookie::queue($cookie);
        // TODO: save for user.
        return $current;
    }

    protected function findCurrentFavorite()
    {
        $favorite = Cookie::get(self::COOKIE_NAME, "[]");
        $favorite = array_values(json_decode($favorite));
        if (empty($favorite) && Auth::check()) {
            // TODO: get from user.
        }
        return $favorite;
    }
}