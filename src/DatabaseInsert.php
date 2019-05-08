<?php
namespace Larafortp;

use Illuminate\Support\Facades\DB;

/*
 * 插入表数据
 * 自动处理menu和node的关系
 * 使用lavaral的DB，这个类只能在lavaral下执行
 */
class DatabaseInsert{
    private static $menu_id;//菜单的节点id
    private static $pid;//父节点的id，即控制器的id的

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
    public static function insertAll($data){
        foreach ($data as $key => $datum) {
            self::insert($key,$datum);
        }
    }

    /*
     * $menuData 为字符串
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
    public static function insert($menuData,$controlAction){
        if(!self::insertMenu($menuData)) exit('菜单插入异常');
        foreach ($controlAction as $item) {
            if(self::insertNodeContronller($item['controller'])){
                unset($data);
                $data = array();
                $data['name'] = $item['name'];
                $data['title'] = $item['title'];
                $data['sort'] = isset($item['sort'])? (int)$item['sort'] : 0;
                $data['icon'] = isset($item['icon'])? $item['icon'] : '';
                $data['remark'] = isset($item['remark'])? $item['remark'] : '';
                $data['status'] = isset($item['status'])? $item['status'] : 1;
                self::insertNodeAction($data);
            }

        }

    }
    /*
     *插入数据到menu
     * $tableData =array('title'=>);
     * $tablData = 'string';
     * 注意只有title是必填项
     */
    public static function insertMenu($tableData){
        $data = self::createMenuDataArray($tableData);
        //获取数据
        $firstData = DB::table('qs_menu')->where('title', $data['title'])->orderBy('id')->first();
        if(!empty($firstData)){
            self::$menu_id = $firstData->id;
        }else{
            self::$menu_id = DB::table('qs_menu')->insertGetId($data);
        }
        return self::$menu_id;
    }

    /*
     * 创建node数据中的控制器数据，
     * 数据格式应为
     *$tableData =array('name'=>);
     * $tablData = 'string';
     */
    public static function insertNodeContronller($tableData){
        $data = self::createNodeController($tableData);
        $firstData = DB::table('qs_node')->where('name', $data['name'])->orderBy('id')->first();
        if(!empty($firstData)){
            self::$pid = $firstData->id;
        }else{
            self::$pid = DB::table('qs_node')->insertGetId($data);
        }
        return self::$pid;
    }
    /*
     * 创建node数据中的方法数据，传值为
     * $data['name'] 和 $data['title'] 必须
     */
    public static function insertNodeAction($tableData){
        $data = self::createNodeAction($tableData);
        //注意没有查重
        $id = DB::table('qs_node')->insertGetId($data);
        if(empty($id)){
            return false;
        }else{
            return $id;
        }
    }

    /*
     *创建menu数据
     */
    private static function createMenuDataArray($tableData){
        $data = array();
        if( is_array( $tableData ) )
        {
            //检验标题
            $data['title'] = (isset($tableData['title']) && !empty($tableData['title']))? $tableData['title']: exit('title为空请检查');
            $data['sort']  = isset($tableData['sort'])? (int)$tableData['sort'] : 0;
            $data['icon']  = isset($tableData['icon'])? $tableData['icon'] : '';
            $data['type']  = isset($tableData['type'])? $tableData['type'] : 'backend_menu';
            $data['url']  = isset($tableData['url'])? $tableData['url'] : '';
            $data['pid']  = isset($tableData['pid'])? (int)$tableData['pid'] : 3;
            $data['module']  = isset($tableData['module'])? $tableData['module'] : '';
            $data['status']  = isset($tableData['status'])? (int)$tableData['status'] : 1;
            $data['level']  = isset($tableData['level'])? (int)$tableData['level'] : 2;
        }
        else
        {
            if(empty($tableData)){
                exit('错误，创建了空菜单数据');
            }
            $data = array(
                'title' => $tableData,
                'sort' => 10,
                'icon' => 'fa-list',
                'type' => 'backend_menu',
                'url' => '',
                'pid' => 3,
                'module' => '',
                'status' => 1,
                'level' => 2,
            );
        }
        return $data;
    }
    /*
     *创建node 控制器的数据
     * 数组$data['name'] 必须
     * 如果是传string，则需要传控制器名，
     */
    private static function createNodeController($data){
        $ControllerData = array();
        if( is_array( $data ) )
        {
            //检验标题
            $ControllerData['name'] = (isset($data['name']) && !empty($data['name']))? $data['name']: exit('控制器名为空请检查');
            $ControllerData['title']  = $ControllerData['name'];
            $ControllerData['status']  = isset($data['status'])? (int)$data['status'] : 1;
            $ControllerData['remark']  = isset($data['remark'])? $data['remark'] : '';
            $ControllerData['sort']  = isset($data['sort'])? (int)$data['sort'] : 0;
            $ControllerData['pid']  = (isset($data['pid']) && !empty($data['pid']))? (int)$data['pid'] : 1;
            $ControllerData['level']  = isset($data['level'])? (int)$data['level'] : 2;
            $ControllerData['menu_id']  = isset($data['menu_id'])? (int)$data['menu_id'] : 0;
            $ControllerData['icon']  = isset($data['icon'])? $data['icon'] : '';
            $ControllerData['url']  = isset($data['url'])? $data['url'] : '';
        }else{
            if(empty($data)){
                exit('错误，创建了空控制器数据');
            }
            $ControllerData = array(
                'name' => $data, //控制器
                'title' => $data,//控制器
                'status' => 1,//状态
                'remark' => '',//备注
                'sort' => 0,//排序
                'pid' => 1,//admin
                'level' => 2,//等级
                'menu_id' => 0,
                'icon' => '',//图标
                'url' => '',//链接
            );

        }
        return $ControllerData;
    }
    /*
     *创建node action数据（即方法、函数）
     * 注意必填项为$data['name']、$data['title']
     */
    private static function createNodeAction($data){
        $actionData = array();
        if( is_array( $data ) )
        {
            //检验标题
            $actionData['name'] = (isset($data['name']) && !empty($data['name']))? $data['name']: exit('方法名名为空请检查');
            $actionData['title']  = (isset($data['title']) && !empty($data['title']))? $data['title'] : exit('标题为空请检查');
            $actionData['status']  = (isset($data['status']) && !empty($data['status']))? (int)$data['status'] : 1;
            $actionData['remark']  = isset($data['remark'])? $data['remark'] : '';
            $actionData['sort']  = isset($data['sort']) ? (int)$data['sort'] : 0;
            $actionData['pid']  = (isset($data['pid']) && !empty($data['pid']))? (int)$data['pid'] : self::$pid;
            $actionData['level']  = (isset($data['level'])  && !empty($data['level']))? (int)$data['level'] : 3;
            $actionData['menu_id']  = (isset($data['menu_id']) && !empty($data['menu_id'])) ? (int)$data['menu_id'] : self::$menu_id;
            $actionData['icon']  = isset($data['icon'])? $data['icon'] : '';
            $actionData['url']  = isset($data['url'])? $data['url'] : '';
        }else{
            exit('错误，方法的数据格式不正确');
        }
        return $actionData;
    }


}