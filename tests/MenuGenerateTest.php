<?php

namespace Larafortp\Tests;

use Larafortp\MenuGenerate;

class MenuGenerateTest extends TestCase
{
    private $menuData = array(
        '测试模块'=>array(
            array(
                'name'=>'index',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'Index',
                'status'=>1,
            ),
            array(
                'name'=>'index',
                'title'=>'新闻中心',
                'sort' => 1,
                'controller'=>'News',
                'status'=>1,
            ),
            array(
                'name'=>'index',
                'title'=>'集团机构',
                'controller'=>'Group'
            ),
        ),
    );
    //$menuData  -> MenuGenerate测试列表
    /**
     * 添加菜单
     */
    //异常
    public function testAddMenuException(){
        //空白菜单
        $this->menuData[''] = array(
            array(
                'name'=>'index',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'Index1',
                'status'=>1,
            ),
        );
        try{
            MenuGenerate::insertAll($this->menuData);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'错误，菜单数据为空');
    }
    //成功
    public function testAddMenuSuccess(){
        try{
            MenuGenerate::insertAll($this->menuData);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_menu', ['title' => '测试模块', 'type' => 'backend_menu', 'level' => 2]);
    }
    /**
     * 添加控制器
     */
    //异常
    public function testAddControllException(){
        //空白控制器
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'index',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'',
                'status'=>1,
            ),
        );
        try{
            MenuGenerate::insertAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'控制器名为空请检查');
    }
    //成功
    public function testAddControllSuccess(){
        try{
            MenuGenerate::insertAll($this->menuData);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_node', ['name' => 'Index','title' => 'Index', 'level' => 2,'pid'=>1]);
    }
    /**
     * 添加节点（方法）
     */
    //异常
    public function testAddNodeException(){
        //空白
        $data['添加'] = array(
            array(
            ),
        );
        try{
            MenuGenerate::insertAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'数据为空');
        //方法名
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'IsControll',
                'status'=>1,
            ),
        );
        try{
            MenuGenerate::insertAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'方法名名为空请检查');
        //标题
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'index',
                'title'=>'',
                'sort' => 0,
                'controller'=>'IsControll',
                'status'=>1,
            ),
        );
        try{
            MenuGenerate::insertAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'标题为空请检查');
    }
    //成功
    public function testAddNodeSuccess(){
        $menuData = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'sort' => 0,
                    'controller'=>'Index',
                    'status'=>1,
                ),
            )
        );
        try{
            MenuGenerate::insertAll($menuData);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_node', ['name' => 'index','title' =>'首页轮播图',  'level' => 3,'pid'=>MenuGenerate::$node_pid]);
    }


    //多层级插入  -> MenuGenerate测试列表
    /**
     *     插入top_menu
     */
    //异常
    public function testAddTopMenuException(){
        $data = array(
            array(
                'title'=>'',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'菜单title为空请检查');
    }
    //成功
    public function testAddTopMenuSuccess(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_menu', ['id' => MenuGenerate::$menu_pid,'title' => '平台2', 'level' => 1,'pid'=>0,'module'=>'newsAdmin']);
    }
    /**
     * 创建模块
     */
    //异常
    public function testAddModuleException(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'模块创建异常,模块名为空');
    }
    //成功
    public function testAddModuleSuccess(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_node', ['id' => MenuGenerate::$module_id,'name' => 'newsAdmin','title' => '后台管理', 'level' => 1,'pid'=>0,'menu_id'=>0]);
    }

    /**
     * 添加菜单
     */
    //异常
    public function testAddBackMenuException(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    ''=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'菜单title为空请检查');
    }
    //成功
    public function testAddBackMenuSuccess(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_menu', ['id' => MenuGenerate::$menu_id,'title' => '新闻中心', 'level' => 2,'pid'=>MenuGenerate::$menu_pid]);
    }
    /**
     * 添加控制器
     */
    //异常
    public function testControllException(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'控制器名为空请检查');
    }
    //成功
    public function testControllSuccess(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'NewsController',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_node', ['id' => MenuGenerate::$node_pid,'name' => 'NewsController','title' => 'NewsController', 'level' => 2,'pid'=>MenuGenerate::$module_id,'menu_id'=>0]);
    }
    //
    /**
     * 添加方法节点
     */
    //异常
    public function testNodeException(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'NewsController',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'方法名名为空请检查');
        //
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'',    //（必填）'
                            'controller'=>'NewsController',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            $m = $e;
        }
        $this->assertEquals($m->getMessage(),'标题为空请检查');
    }
    //成功
    public function testNodeSuccess(){
        $data = array(
            array(
                'title'=>'平台2',//标题              (必填)
                'module'=>'newsAdmin',//模块英文名        (必填)
                'module_name'=>'后台管理',//模块中文名   (必填)
                'url'=>'',//url                  (必填)
                'type'=>'',//类型                (选填）
                'sort'=>0,//排序                (选填）
                'icon'=>'',//icon                (选填）
                'status'=>1,//状态              (选填）
                'top_menu' => array(
                    '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'NewsController',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                    ),
                ),
            ),
        );
        try{
            MenuGenerate::insertNavigationAll($data);
        }catch (\Exception $e){
            echo $e;
        }
        $this->assertDatabaseHas('qs_node', ['name' => 'index','title' => '测试新闻中心', 'level' => 3,'pid'=>MenuGenerate::$node_pid,'menu_id'=>MenuGenerate::$menu_id]);
    }
}
