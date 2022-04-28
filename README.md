## 1、解决完成 就是路由缓存没清 导致一直加载不来 php artisan route:clear
    Route::get('index',[TaskController::class,'index']);
    

修改php.ini配置文件可以让路由不缓存opcache--opcache.enable=0

#### PS:laravel部署在服务器只有首页访问到，其他页面404 ####
      
修改ngix配置文件

    server {
	    listen80;
	    server_name  localhostlaravel;
	    root   "D:/phpstudy_pro/WWW/laravel/public";
	    location / {
	    index index.php index.html error/index.html;
	    #新增底下
	    try_files $uri $uri/ /index.php?$query_string;
	    }
    }

## 2、路由联链接对应控制器 #
    Route::get('index',[TaskController::class,'index']);
    Route::get('task',[TaskController::class,'index']);
    Route::get('task/getParam/{id}',[TaskController::class,'getParam']);
    Route::match(['get','post'],'getParam/{id}',[TaskController::class,'getParam'])->where('id','.*')->name('task.getParam');
    
## 3、路由重定向 302重定向跟301重定向  (注意游览器缓存) ##
    Route::redirect('tasks','task'); //302临时跳转
    Route::redirect('indexs','task','301'); //301永久性跳转
    Route::permanentRedirect('indexS','task'); //301跳转

## 4、视图加载 ##
知识点phpStorm 视图输入 ！+ tab 可以快速生成html头文件等代码  输入div+tab也可以快速生成标签

    Route::view('viewTest','viewTest');
`Route::view('viewTest1','viewTest',['id'=>10]); ` //带参数加载视图 前端直接用{{$id}}

    Route::get('viewTest2/{id}',function ($id){
       return view('viewTest',['id'=>$id]);
    });//助手函数加载视图

`Route::get('viewTest3',[TaskController::class,'index']);`//通过控制器加载视图(常用)

## 5、路由分组 与 路由命名 ##
  ！！！知识点 按住ctrl点函数方法 可以跳转到函数定义部分
  //PS url 是 uri 的子集   路由命名规范：控制器.方法
    `Route::get('task/url',[TaskController::class,'url'])->name('task.url'); `
//控制器中使用助手函数route('name') 可以获取到这个别名路由的url地址 http://localhost:8000/task/url
//加参 route助手函数第二个参数为参数 第三个参数为是否包含域名   （通过别名来重定向，通过第二个携带参数）
    `Route::get('task/url1',[TaskController::class,'url'])->name('task.url');`

//路由分组：为了让大量路由共享路由属性，包括中间件、命名空间等；
`Route::prefix('api')->get('taskPre',[TaskController::class,'index']);` //路由前缀 (分组之后可以共用)

//一个空的分组路由

    Route::group([],function (){
    
    Route::get('taskPre1',[TaskController::class,'index']); //路由前缀 (分组之后可以共用)
    Route::get('taskPre2',function (){
    return "123";
    }); //路由前缀 (分组之后可以共用)
    
    });

//加上路由前缀的(方法一)

    Route::group(['prefix'=>'api'],function (){
    
    Route::get('taskPre3',[TaskController::class,'index']); //路由前缀 (分组之后可以共用)
    Route::get('taskPre4',function (){
    return "123";
    }); //路由前缀 (分组之后可以共用)
    
    });
//加上路由前缀的(方法二)
    
    Route::prefix('api')->group(function (){
    
    Route::get('taskPre3',[TaskController::class,'index']); //路由前缀 (分组之后可以共用)
    Route::get('taskPre4',function (){
    return "123";
    }); //路由前缀 (分组之后可以共用)
    
    });


//中间件

    Route::middleware('中间件')->group(function (){});

//子域名

    Route::domain('127.0.0.1')->group(function (){
    Route::get('task/domain',[TaskController::class,'index']);
    });

//命名空间 同一命名

    Route::namespace('Admin')->group(function (){
    Route::get('manage',[ManageController::class,'index']);
    });

//名称前缀 支持嵌套 task.abc.index
    
    Route::name('task.')->group(function (){}); //方法二的 数组中的是 ['as'=>"task."]

## 6、回退 当前路由 单行为 ##
//单行为控制器  

    php artisan make:controller OneController --invoker

    Route::get('one',OneController::class);

//使用404页面跳转

    //这个放在所有路由访问的最后面
    //Route::fallback(function (){
    //return view('404');
    //}); 

//当前路由 (调试使用)

    Route::get('current1',[TaskController::class,'index']);

    Route::get('current2',function (){
    //dump(Route::current()); //当前路由信息(请求方法、头信息等)
    return Route::currentRouteName(); //当前路由名字
    //return Route::currentRouteAction(); //当前控制器等信息 在控制器输出才有 App\Http\Controllers\TaskController@index
    })->name('current2');

## 7、响应设置 ##
    
    Route::get('redirect1',function (){
    return response("123",404);
    });
    Route::get('response',function (){
    return response('<p>12323</p>')->header('Content-Type','text/plain');
    });

## 8、重定向 ##

    Route::get('redirect2',function (){
       return redirect('index');
    });
    Route::get('redirect3',function (){
    return redirect()->to('index');
    });
    Route::get('redirect4',function (){
    return redirect()->route('task.url');
    });
    Route::get('redirect5',function (){
    return redirect()->action([OneController::class]);
    });
    Route::get('redirect6',function (){
    return redirect()->away('http://www.baidu.com');
    });
    Route::get('redirect7',function (){
    return redirect()->back();
    });
## 9、资源控制器 ##
 - 有一种控制器专门处理CURD(增删改查)，方法很多且方法名基本固定 -- 对于可以设置为资源控制器 不要大量设置路由

//使用命令行 生成 资源控制器

     php artisan make:controller BlogController --resource
//自动生成7个方法










//路由回退 访问不存在的路由时404 回退路由可以让不存在的路由跳转到指定页面去  ：： 回退路由放在所有路由的最底部

    Route::fallback(function (){
    return view('404');
    });


