<?php
/**
 * Created by PhpStorm.
 * User: binhnt
 * Date: 2/10/14
 * Time: 12:53 PM
 */
namespace User\Controller;
class FavoriteItem extends UserBase
{

    protected $user;

    /**
     * Require login
     */
    public function beforeExecute()
    {
        $this->user = \UserAuth::getInstance()->getUser();
        if (empty($this->user)) {
            $this->request()->redirect(
                $this->createUrl('login', array('url' => base64_encode($this->request()->getUri())))
            );
        }
    }

    public function executeDefault()
    {

    }

    /**
     * Like item
     * @return bool|string
     */
    public function executeLike()
    {
        $req = $this->request();
        $id = $req->post('id', 'INT', 0);
        $type = $req->post('type');

        // Render text return
        return $this->renderText($this->__like($id, $type));

    }

    protected function __like($id, $type) {
        if($id == 0) {
            return false;
        }
        $existItem = null;
        $favData = array(
            'item_id' => '', 'image' => '', 'link' => '', 'homeland' => '', 'props' => ''
        );
        switch (strtolower($type)) {
            case 'cart': // Retrieve item from cart
                $item = \CartItem::retrieveById($id);
                $link = $item->getLinkOrigin();
                // Check exist
                if (!empty($link)) { // Check item from other site
                    $existItem = \FavoriteItem::findByItemIdAndHomeland($item->getItemId(), $item->getSite());
                }
                // Convert to array
                if ($item) {
                    $favData['item_id'] = $item->getId();
                    $favData['title'] = $item->getTitle();
                    $favData['image'] = $item->getItemImg();
                    $favData['link'] = $item->getLinkOrigin();
                    $favData['homeland'] = $item->getSite();
                    $favData['props'] = json_encode(array(
                        'price' => $item->getPriceVnd(),
                        'attr' => $item->getPropertiesTranslate()
                    ));
                }
                break;
            case 'order': // Read item from order
                $item = \OrderItem::retrieveById($id);

                break;
            case 'item': // Retrieve item from system
                $item = null;
                break;
            default:
                $item = new \CartItem();
                break;
        }
        $fav = new \FavoriteItem();

        if (empty($existItem)) {
            $fav->setUserId($this->user->getId());
            $fav->setItemId($favData['item_id']);
            $fav->setTitle($favData['title']);
            $fav->setImage($favData['image']);
            $fav->setHomeland($favData['homeland']);
            $fav->setLink($favData['link']);
            $fav->setProps($favData['props']);
            $fav->setCreateTime(date('Y-m-d H:i:s'));

            return $fav->save() ? true : false;
        }
        return false;
    }

    public function executeUnLike()
    {
        $req = $this->request();
        $id = $req->get('id', 'INT', 0);
        $item = \FavoriteItem::retrieveById($id);

        return $this->renderText($item->delete() ? true : false);
    }
}