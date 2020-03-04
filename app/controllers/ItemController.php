<?php
namespace app\controller;

use app\model\ItemModel;
use bestphp\base\Controller;

/**
 * ItemController
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/4 17:17
 */
class ItemController extends Controller
{
    // 首页方法, 测试框架自定义DB查询
    public function index(ItemModel $itemModel)
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        if ($keyword) {
            $items = $itemModel->search($keyword);
        } else {
            // 查询所有内容, 并按倒序排列输出
            // where()方法可不传入参数, 或者省略
            $items = $itemModel->where()->order(['id DESC'])->fetchAll();
        }

        $this->assign('title', '全部条目');
        $this->assign('keyword', $keyword);
        $this->assign('items', $items);
        $this->render();
    }

    /**
     * 查看单条记录详情
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 17:27
     */
    public function detail()
    {
        // 通过?占位符传入$id参数
        $item = (new ItemModel)->where(['id = ?'])->fetch();
        $this->assign('title', '条目详情');
        $this->assign('item', $item);
        $this->render();
    }

    /**
     * 添加记录，测试框架DB记录创建（Create）
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 17:30
     */
    public function add()
    {
        $data['item_name'] = $_POST['value'];
        $count = (new ItemModel)->add($data);

        $this->assign('title', '添加成功');
        $this->assign('count', $count);
        $this->render();
    }

    /**
     * 操作管理
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 17:32
     * @param int $id
     */
    public function manage($id = 0)
    {
        $item = array();
        if ($id) {
            // 通过名称占位符传入参数
            $item = (new ItemModel())->where(["id = :id"], [':id' => $id])->fetch();
        }

        $this->assign('title', '管理条目');
        $this->assign('item', $item);
        $this->render();
    }

    /**
     * 更新记录，测试框架DB记录更新
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 17:32
     */
    public function update()
    {
        $data = ['id' => $_POST['id'], 'item_name' => $_POST['value']];
        $count = (new ItemModel)->where(['id = :id'], [':id' => $data['id']])->update($data);

        $this->assign('title', '修改成功');
        $this->assign('count', $count);
        $this->render();
    }

    /**
     * 删除记录，测试框架DB记录删除
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 17:34
     * @param null $id
     */
    public function delete($id = null)
    {
        $count = (new ItemModel)->delete($id);

        $this->assign('title', '删除成功');
        $this->assign('count', $count);
        $this->render();
    }
}