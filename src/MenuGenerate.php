<?php

namespace Larafortp;

use Illuminate\Support\Facades\DB;

/*
 * 生成菜单和节点列表
 * 自动处理menu和node的关系
 *
 */
class MenuGenerate
{
    public static $menu_id = 0; //菜单的节点id      node::level=3时node::pid使用这个值，
    public static $node_pid = 0; //父节点的id，即控制器的id的 node::level=3时pid使用这个值
    public static $menu_pid = 0; //菜单的pid     menu::level=2时menu pid使用这个值（这个值产生于插入头部导航栏）
    public static $module_id = 0; //模块id        node::level=2时pid使用这个值

        /*
         * 多条插入，一般只用这个函数
         *用法：
        参数格式：
        $data = array(
                '菜单名称(必填)'=>array(
                         array(
                                'name'=>'方法名',       //（必填）
                                'title'=>'节点名称',    //（必填）
                                'controller'=>'控制器名称',//（必填）
                                'sort' => 1, //排序       //（选填）
                                'icon'=> '',//图标        //（选填）
                                'remark'=> '',//备注      //（选填）
                                'status'=>1,//状态        //（选填）
                        ),
                        ......
                 ),
                ......
            );
        例如：
                $data = array(
                '新闻中心1'=>array(
                        array(
                            'name'=>'index',
                            'title'=>'内容管理',
                            'controller'=>'News',
                        ),
                        array(
                            'name'=>'index',
                            'title'=>'分类管理',
                            'sort' => 1,
                            'controller'=>'controller1',
                            'status'=>1,
                        ),
                ),
                '集团机构'=>array(
                    array(
                        'name'=>'index',
                        'title'=>'分类管理',
                        'sort' => 1,
                        'controller'=>'controller1',
                        'status'=>1,
                    ),
                ),
            );
         *
         */
    public static function insertAll($data)
    {
        DB::beginTransaction();

        try {
            foreach ($data as $key => $datum) {
                self::insert($key, $datum);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
        DB::commit();
    }

    /*
     *
     *
     $data = array(
                 array(
                        'title'=>''，//标题              (必填)
                        'module'=>'',//模块英文名        (必填)
                        'module_name'=>'',//模块中文名   (必填)
                        'url'=>'',//url                  (必填)
                        'type'=>'',//类型                (选填）注意，选填的内容如果为0或者空，不要填写这一对组值
                        'sort'=>'',//排序                (选填）
                        'icon'=>'',//icon                (选填）
                        'status'=>'',//状态              (选填）
                        'top_menu' => array(
                                    '菜单名称(必填)'=>array(
                                            array(
                                                'name'=>'方法名',       //（必填）
                                                'title'=>'节点名称',    //（必填）
                                                'controller'=>'控制器名称',//（必填）
                                                'sort' => 1, //排序       //（选填）注意，选填的内容如果为0或者空，不要填写这一对组值
                                                'icon'=> '',//图标        //（选填）
                                                'remark'=> '',//备注      //（选填）
                                                'status'=>1,//状态        //（选填）
                                            ),
                                            ......
                                     ),
                                ......
                       ),
                ),
                ......
            );
     *
     *
     */
    public static function insertNavigationAll($data)
    {
        DB::beginTransaction();

        try {

            //取出每一个top_menu
            foreach ($data as $item) {
                self::insertNavigation($item);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
        DB::commit();
    }

    public static function insertNavigation($data)
    {
        //这个方法处理数据的
        $data['type'] = 'top_menu';
        //等级为1
        $data['level'] = 1;
        $data['pid'] = 0;
        //传创建top_menu
        self::insertMenu($data);
        //创建模块
        self::insertNodeModul($data);
        //创建菜单和节点
        foreach ($data['top_menu'] as $key => $item) {
            $menuData['title'] = $key;
            $menuData['level'] = 2;
            $menuData['pid'] = self::$menu_pid;
            self::insert($menuData, $item);
        }
    }

    /*
     * $menuData 为字符串或者数组
     * $contronller 二维数组
     * $contronller = array(
        array(
            name=>//名称
            title=>//标题
            sort=>排序
            icon=> icon
            remark=> 备注
            controller=> 控制器
            status=>状态
        )
     );
     *
     */
    public static function insert($menuData, $controlAction)
    {
        $controller = [];

        try {
            self::insertMenu($menuData);
            foreach ($controlAction as $item) {
                if (empty($item)) {
                    throw new \Exception('数据为空');
                }
                $controller['name'] = $item['controller'];
                if (!empty(self::$module_id)) {
                    $controller['pid'] = self::$module_id;
                    $controller['menu_id'] = 0;
                }
                self::insertNodeContronller($controller);
                self::insertNodeAction($item);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*
     *插入数据到menu
     * $tableData =array('title'=>);
     * $tablData = 'string';
     * 注意只有title是必填项
     * 这个主要处理menu的逻辑关系
     */
    public static function insertMenu($tableData)
    {
        if (empty($tableData)) {
            throw new \Exception('insertMenu Null');
        }
        /*
         * 菜单数据有两种level=1,为头部导航，level=2为左边导航
         */
        if (is_array($tableData)) {
            //获取数据
            if (isset($tableData['level']) && $tableData['level'] == 1) {
                $firstData = DB::table('qs_menu')->where('title', $tableData['title'])->where('level', 1)->first();
            } else {
                if (!empty(self::$menu_pid)) {
                    $firstData = DB::table('qs_menu')->where('title', $tableData['title'])->where('level', 2)->where('pid', self::$menu_pid)->first();
                } else {
                    $firstData = DB::table('qs_menu')->where('title', $tableData['title'])->where('level', 2)->first();
                }
            }
            if (isset($firstData->level) && $firstData->level == 1) {
                self::$menu_pid = $firstData->id;
            } elseif (isset($firstData->level) && $firstData->level == 2) {
                self::$menu_id = $firstData->id;
            } else {
                $data = self::createMenuDataArray($tableData);
                self::$menu_id = DB::table('qs_menu')->insertGetId($data);
                if ($data['level'] == 1) {
                    self::$menu_pid = self::$menu_id;
                }
            }
        } else {
            $firstData = DB::table('qs_menu')->where('title', $tableData)->where('level', 2)->first();
            if (empty($firstData)) {
                $data = self::createMenuDataArray($tableData);
                self::$menu_id = DB::table('qs_menu')->insertGetId($data);
            } else {
                self::$menu_id = $firstData->id;
            }
        }
    }

    //处理模块的逻辑关系
    public static function insertNodeModul($tableData)
    {
        if (empty($tableData['module']) || empty($tableData['module_name'])) {
            throw new \Exception('模块创建异常,模块名为空');
        }
        $firstData = DB::table('qs_node')->where('name', $tableData['module'])->where('level', 1)->first();
        if (empty($firstData)) {
            $data = [];
            $data['name'] = $tableData['module'];
            $data['title'] = $tableData['module_name'];
            $data['sort'] = 0;
            $data['pid'] = 0;
            $data['level'] = 1;
            $data['menu_id'] = 0;
            $data['icon'] = '';
            $data['remark'] = '';
            $data['status'] = 1;
            self::$module_id = DB::table('qs_node')->insertGetId($data);
        } else {
            self::$module_id = $firstData->id;
        }
    }

    /*
     * 创建node数据中的控制器数据，
     * 数据格式应为
     *$tableData =array('name'=>);
     * $tablData = 'string';
     * 这个处理控制器的逻辑关系
     */
    public static function insertNodeContronller($tableData)
    {
        //查找控制器是否存在
        //添加控制器
        if (!empty(self::$module_id)) {
            $tableData['pid'] = self::$module_id;
        } else {
            $tableData['pid'] = 1;
        }
        $data = self::createNodeController($tableData);
        if (!empty(self::$module_id)) {
            $firstData = DB::table('qs_node')->where('name', $data['name'])->where('pid', self::$module_id)->where('level', 2)->first();
        } else {
            $firstData = DB::table('qs_node')->where('name', $data['name'])->where('level', 2)->where('pid', 1)->first();
        }
        if (!empty($firstData)) {
            self::$node_pid = $firstData->id;
        } else {
            self::$node_pid = DB::table('qs_node')->insertGetId($data);
        }
    }

    /*
     * 创建node数据中的方法数据，传值为
     * $data['name'] 和 $data['title'] 必须
     */
    public static function insertNodeAction($tableData)
    {
        $data = self::createNodeAction($tableData);
        $map = [];
        //如果一下数据在数据库的某条记录中已经存在，则认为重复了
        $map['name'] = $data['name'];
        $map['title'] = $data['title'];
        $map['level'] = $data['level'];
        $map['pid'] = $data['pid'];
        $map['menu_id'] = $data['menu_id'];
        //查重
        $repeat = DB::table('qs_node')->where($map)->first();
        //重复直接返回
        if (!empty($repeat)) {
            return true;
        }
        $id = DB::table('qs_node')->insertGetId($data);
        if (empty($id)) {
            throw new \Exception($data['name'].'方法创建异常');
        } else {
            return $id;
        }
    }

    /*
     *创建menu数据
     * 负责数据生成
     */
    private static function createMenuDataArray($tableData)
    {
        $data = [];
        if (is_array($tableData)) {
            //检验标题
            if (isset($tableData['title']) && !empty($tableData['title'])) {
                $data['title'] = $tableData['title'];
            } else {
                throw new \Exception('菜单title为空请检查');
            }
            $data['sort'] = isset($tableData['sort']) ? (int) $tableData['sort'] : 0;
            $data['icon'] = isset($tableData['icon']) ? $tableData['icon'] : '';
            $data['type'] = (isset($tableData['type']) && !empty($tableData['type'])) ? $tableData['type'] : 'backend_menu';
            $data['url'] = isset($tableData['url']) ? $tableData['url'] : '';
            $data['pid'] = isset($tableData['pid']) ? $tableData['pid'] : self::$menu_pid;
            $data['module'] = isset($tableData['module']) ? $tableData['module'] : '';
            $data['status'] = isset($tableData['status']) ? (int) $tableData['status'] : 1;
            $data['level'] = isset($tableData['level']) ? (int) $tableData['level'] : 2;
        } else {
            if (empty($tableData)) {
                throw new \Exception('错误，菜单数据为空');
            }
            $data = [
                'title'  => $tableData,
                'sort'   => 10,
                'icon'   => 'fa-list',
                'type'   => 'backend_menu',
                'url'    => '',
                'pid'    => 3,
                'module' => '',
                'status' => 1,
                'level'  => 2,
            ];
        }

        return $data;
    }

    /*
     *创建node 控制器的数据
     * 数组$data['name'] 必须
     * 如果是传string，则需要传控制器名，
     */
    private static function createNodeController($data)
    {
        $ControllerData = [];
        //检验标题
        if (isset($data['name']) && !empty($data['name'])) {
            $ControllerData['name'] = $data['name'];
        } else {
            throw new \Exception('控制器名为空请检查');
        }
        $ControllerData['title'] = $ControllerData['name'];
        $ControllerData['status'] = isset($data['status']) ? (int) $data['status'] : 1;
        $ControllerData['remark'] = isset($data['remark']) ? $data['remark'] : '';
        $ControllerData['sort'] = isset($data['sort']) ? (int) $data['sort'] : 0;
        $ControllerData['pid'] = (isset($data['pid']) && !empty($data['pid'])) ? (int) $data['pid'] : (empty(self::$node_pid) ? 1 : self::$node_pid);
        $ControllerData['level'] = isset($data['level']) ? (int) $data['level'] : 2;
        $ControllerData['menu_id'] = isset($data['menu_id']) ? (int) $data['menu_id'] : (empty(self::$menu_pid) ? 0 : self::$menu_pid);
        $ControllerData['icon'] = isset($data['icon']) ? $data['icon'] : '';
        $ControllerData['url'] = isset($data['url']) ? $data['url'] : '';

        return $ControllerData;
    }

    /*
     *创建node action数据（即方法、函数）
     * 注意必填项为$data['name']、$data['title']
     */
    private static function createNodeAction($data)
    {
        $actionData = [];
        if (is_array($data) && !empty($data)) {
            //检验标题
            if ((isset($data['name']) && !empty($data['name']))) {
                $actionData['name'] = $data['name'];
            } else {
                throw new \Exception('方法名名为空请检查');
            }
            if (isset($data['title']) && !empty($data['title'])) {
                $actionData['title'] = $data['title'];
            } else {
                throw new \Exception('标题为空请检查');
            }
            $actionData['status'] = (isset($data['status']) && !empty($data['status'])) ? (int) $data['status'] : 1;
            $actionData['remark'] = isset($data['remark']) ? $data['remark'] : '';
            $actionData['sort'] = isset($data['sort']) ? (int) $data['sort'] : 0;
            $actionData['pid'] = (isset($data['pid']) && !empty($data['pid'])) ? (int) $data['pid'] : self::$node_pid;
            $actionData['level'] = (isset($data['level']) && !empty($data['level'])) ? (int) $data['level'] : 3;
            $actionData['menu_id'] = (isset($data['menu_id']) && !empty($data['menu_id'])) ? (int) $data['menu_id'] : self::$menu_id;
            $actionData['icon'] = isset($data['icon']) ? $data['icon'] : '';
            $actionData['url'] = isset($data['url']) ? $data['url'] : '';
        } else {
            throw new \Exception('错误，方法的数据格式不正确');
        }

        return $actionData;
    }
}
