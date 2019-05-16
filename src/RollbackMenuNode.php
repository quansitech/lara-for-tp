<?php
namespace Larafortp;

use Illuminate\Support\Facades\DB;
/*
 * 回滚所创建的菜单节点
 */

class RollbackMenuNode
{
    private static $menu_pid=3;//菜单的pid     默认为平台
    private static $module_id=1;//模块id       默认为admin
    /*
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
     */
    /**
     * 使用insertNavigationAll生成则是这个方法删除
     * @param $data
     * @throws \Exception
     */
    public static function insertNavigationAllDelete($data)
    {
        DB::beginTransaction();
        try{

            //取出每一个top_menu
            foreach ($data as $item) {
                self::setMenuPID($item['title']);
                self::setModuleId($item['module']);
                //处理菜单列表
                foreach ($item['top_menu'] as $key => $menu) {
                    self::handleMenuNode($key,$menu);
                }
                if(!self::countChildrenMenu(self::$menu_pid)){
                    self::deleteMenu(self::$menu_pid);
                }
                if(!self::countChildrenNode(self::$module_id)){
                    self::deleteNode(self::$module_id);
                }
            }
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
        DB::commit();
    }

    /**
     * * 使用indertAll创建数据，则必须使用这个方法回滚
     * @param $data
     * @throws \Exception
     */
    public static function insertAllDelete($data)
    {
        DB::beginTransaction();
        try{
            foreach ($data as $key => $datum) {
                self::handleMenuNode($key,$datum);
            }
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
        DB::commit();
    }
    public static function handleMenuNode($title,$node)
    {
        $menu = self::queryMenu($title,2,self::$menu_pid);
        foreach ($node as $item) {
            self::handleNode($item);
        }
        //删除菜单
        if(!self::countMenuChildrenNode($menu->id)){
            self::deleteMenu($menu->id);
        }
    }
    /**
     * 处理 设置模块id
     * @param $moduleName
     * @throws \Exception
     */
    public static function setModuleId($moduleName)
    {
        $modu = self::queryNode($moduleName,1,0);
        if(!empty($modu)){
            self::$module_id = $modu->id;
        }else{
            throw new \Exception('1、未能查找到“'.$moduleName.'”模块名');
        }
    }

    /**
     * 设置top_menu menu_pid
     * @param $menuName
     * @throws \Exception
     */
    public static function setMenuPID($menuName)
    {
        $modu = self::queryMenu($menuName,1,0);
        if(!empty($modu)){
            self::$menu_pid = $modu->id;
        }else{
            throw new \Exception('1、未能查找到“'.$menuName.'”菜单名');
        }
    }

    /**
     * 处理 backend_menu
     * @param $menuName
     * @throws \Exception
     */
    public static function handleMenu($menuName)
    {
        $menu = self::queryMenu($menuName,2,self::$menu_pid);
        if(!empty($menu)){
            self::deleteMenu($menu->id);
        }else{
            throw new \Exception('2、未能查找到“'.$menuName.'”菜单名');
        }
    }

    /**
     *处理节点的逻辑关系
     * @param $data  这是一个数组
     * @throws \Exception
     */
    public static function handleNode($data)
    {
//        $data=array(
//            'name'=>'方法名',       //（必填）
//            'title'=>'节点名称',    //（必填）
//            'controller'=>'控制器名称',//（必填）
//            'sort' => 1, //排序       //（选填）注意，选填的内容如果为0或者空，不要填写这一对组值
//            'icon'=> '',//图标        //（选填）
//            'remark'=> '',//备注      //（选填）
//            'status'=>1,//状态        //（选填）
//        );
        //获取控制器
        $controller = self::queryNode($data['controller'],2,self::$module_id);
        if(empty($controller)){
            //控制器不存在
            throw new \Exception('回滚错误，“'.$data['controller'].'”控制器不存在');
        }
        $node = self::queryNode($data['name'],3,$controller->id);
        //删除节点
        if(!empty($node)){
            self::deleteNode($node->id);
        }else{
            throw new \Exception('回滚错误，“'.$data['name'].'”节点方法不存在');
        }
        //删除控制器
        if(!self::countChildrenNode($controller->id)){
            self::deleteNode($controller->id);
        }
    }
    /**查询menu
     * @param $title
     * @param $level
     * @param $pid
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function queryMenu($title,$level,$pid)
    {
        return DB::table('qs_menu')->where('title',$title)
            ->where('level',$level)
            ->where('pid',$pid)
            ->first();
    }

    /**查询menu
     * @param $name 菜单名
     * @param $level  菜单等级
     * @param $pid 菜单的父节点
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function queryNode($name,$level,$pid)
    {
      return DB::table('qs_node')->where('name',$name)
          ->where('level',$level)
          ->where('pid',$pid)
          ->first();
    }

    /**查询id=pid的子节点数
     * @param $pid
     * @return int
     */
    public static function countChildrenMenu($pid)
    {
        return DB::table('qs_menu')->where('pid',$pid)->count();
    }

    /**查询id=pid的子节点数
     * @param $pid
     * @return int
     */
    public static function countChildrenNode($pid)
    {
        return DB::table('qs_node')->where('pid',$pid)->count();
    }

    /**查询菜单下是否有子节点
     * @param $pid
     * @return int
     */
    public static function countMenuChildrenNode($menu_id)
    {
        return DB::table('qs_node')->where('menu_id',$menu_id)->count();
    }
    /**删除菜单
     * @param $id
     * @return int
     */
    public static function deleteMenu($id)
    {
        return DB::table('qs_menu')->delete($id);
    }
    /**删除节点
     * @param $id 节点id
     * @return int
     */
    public static function deleteNode($id)
    {
        return DB::table('qs_node')->delete($id);
    }

}