#《Create Your own PHP Framework》笔记
## 前言
Symfony的学习蛮累的，官方文档虽然很丰富，但是组织方式像参考书而不是指南，一些不错的指导性文档常常是是看组件文档时提到了才偶然发现的，这方面感觉就跟看Laravel和Webpack的官方文档有差距。同时Google中找Symfony的问题也不像Laravel容易找到答案，经常是自己看完整个官方文档结合源码才解决，进度赶的时候真是折磨人。总的来讲，虽然非常非常强大，但是在掌握上，确实不像Laravel那么方便学习。如果从Linux的设计哲学上来讲，我认为Laravel是策略，Symfony是机制。策略的目标是在易用的前提下，提供足够的灵活性；而机制相反，在保证灵活性的情况下，足够易用，比较难学是自然的。策略需要依赖于机制之上，所以Laravel依赖Symfony。

之前在学Laravel时，看了[《如何Composer一步一步构建自己的PHP框架》](https://lvwenhan.com/php/405.html)这个系列，对于Laravel的学习大有裨益。于是在学Symfony时，也是希望有个类似的教程，结果在Symfony官方文档中偶然找到了[《Create Your own PHP Framework》](http://symfony.com/doc/current/create_framework/index.html)，学完后再看Symfony确实清晰了很多。

这里简单做下每一节的笔记，主要记了一些设计思想的点，比较零散，看完原文再来看估计会有所共鸣。

## 笔记
### introduction

> When creating a framework, following the MVC pattern is not the right goal. The main goal should be the Separation of Concerns.”

看到这句话时，我想起之前跟别人谈如何一步步学习Laravel时说：“路由是框架的基石，而在这之上，通过构建MVC的每一层就完成了基本框架；然后再搭配一些现代必备特性比如命令行、测试；以及一些常用服务：队列、安全认证等。就能理解Laravel。”

一下子就被打脸了，Symfony提出了构建框架的主要目标是关注点分离。从理念层面上是对的，MVC不是唯一的解决，不过太过抽象，MVC只是一种具体的关注点分离的方法，对于普通开发者会比较容易掌握。实际上，如果我只想着关注点分离，也还不知道如何下手。

为什么要自己写一个框架？

- 研究Symfony, 这是我的主要目的；
- 根据自己特殊需求做一个自己的框架；
- 纯粹出于探索的乐趣；
- 重构旧代码以便符合现代的最佳实践；
- 证明你自己。。。

[原文：introduction](http://symfony.com/doc/3.2/create_framework/introduction.html)

### The HttpFoundation Componen

即便是最简单的事情，使用框架也好于不使用。再简单的代码都面临以下问题：

- 对参数的判断
- 安全问题，比如XSS攻击；
- 方便单元测试；

再简单的问题如果要满足上面的条件，写出的代码都比使用框架还累。

**如果你认为*安全性*与*可测试性*不足以说服你停止写旧代码，赶紧采用新框架的话 ，那么你可以停止读本书并继续你以前的工作方式了。(深深地感受到作者的高冷)**

框架存在的目的是让你更快地写出更好的代码，而不是让你有所牺牲，如果有什么牺牲的话，我想应该是学习成本的增加吧。

以后就算不使用框架，也应该使用`HttpFoundation`组件的`Request`和`Response`处理请求与响应。

[原文：The HttpFoundation Component](http://symfony.com/doc/3.2/create_framework/http_foundation.html)

### The Front Controller
用于分配路由的控制器称为前端控制器(`Front Controller`)，它根据`$request->getPathInfo()`调用不同目标代码。这个框架到此最大的问题在于路由于于简单，所以下一节应该是解决路由问题。

[原文：The Front Controller](http://symfony.com/doc/3.2/create_framework/front_controller.html)


### The Routing Component
上面简单的路由并不太能满足我们的要求，比如我们想实现路由的通配符匹配就比较麻烦。
因此，使用第三方的路由库是必要的。`symfony/routing`就很方便。这个路由很好，对象却有点多，刚看时还真是不太好理解。

`Routing`组件的基本对象：

- `RouteCollection` 路由集合
- `Route` 单个路由
- `RequestContext` 请求上下文，通过`fromRequest`方法与`Request`绑定。（这种分离有利于测试）
- `UrlMatcher` 将`RouteCollection`与`RequestContext`绑定

然后通过

```
$attributes = $matcher->match($request->getPathInfo());
```

获取当前的路由信息，下面这些实例表明每个路由都会有`_route`这个属性，同时如果定义了通配属性，也会变成对应的变量。

```
print_r($matcher->match('/bye'));
/* Gives:
array (
  '_route' => 'bye',
);
*/

print_r($matcher->match('/hello/Fabien'));
/* Gives:
array (
  'name' => 'Fabien',
  '_route' => 'hello',
);
*/

print_r($matcher->match('/hello'));
/* Gives:
array (
  'name' => 'World',
  '_route' => 'hello',
);
*/
```

另外，当match不到时，会抛出如下异常：`Routing\Exception\ResourceNotFoundException`，
使用Routing有个额外的好处，就是可以从根据路由生成路径：

```
echo $generator->generate(
    'hello',
    array('name' => 'Fabien'),
    UrlGeneratorInterface::ABSOLUTE_URL
);
// outputs something like http://example.com/somewhere/hello/Fabien
```

路由的问题解决了，但是到现在还没控制器，这个后面应该要解决了。

[原文：The Routing Component](http://symfony.com/doc/3.2/create_framework/routing.html)

### Templating


直接渲染模板是有问题的，当业务逻辑稍微复杂一点就无法在模板中完成。因此需要将逻辑与渲染模板分开。

这一节为什么不是直接谈`控制器`呢，我想跟第一节作者提到的`关注点分离`的概念有关，目前为止，框架的问题在于逻辑在模板中做很困难，所以当前事情是要把模板与逻辑抽离出来，本节模板逻辑分离是目的，`控制器`只是惯例做法。

按照`Symfony`的惯例。通过给`Route`的`属性`，增加`_controller`这个键值，它指明路由对应的方法，框架将直接调用`_controller`完成各种不同的工作。

这里有个注意点，路由的属性都被保存到`$request->attributes`中，该属性用保存跟`HTTP`没有直接相关的信息。

增加了`_controller`属性之后，再将`路由信息`剥离到单独一个文件`src/app.php`，现在模板与业务逻辑区分开了。

[原文：Templating](http://symfony.com/doc/3.2/create_framework/templating.html)

### The HtppKernel Component:The Controller Resolver]

上一节为止，所有的操作都是过程化的。我们希望将`_controller`指向一个类的方法，比如`LeapYearController`的`indexAction`。改造起来也很简单。将路由的`_controller`改为`[new LeapYearController(), ‘indexAction’]`即可。

然而这也带来了另外一个缺点，不论路由有没有用到，在它们添加的时候，控制器都被初始化，这对性能是个很大的影响。因此我们希望只有用到的路由才初始化。这个问题可以使用`http-kernel`模块解决。

`http-kernel`提供了非常丰富的功能，不过我们现在只关心`HttpKernel\Controller\ControllerResolver`和`HttpKernel\Controller\ArgumentResolver`。

前者可以用来路由中确定出要调用的方法；后者用来确定要传递给方法的参数；参数解析器使用了反射机制，以便实现`依赖注入`和将路由的`attributes`的同名参数传递进去。调用路由方法与传参，自己做还是要费一定功夫的，所以使用这两个解析器都是必须的。

[原文：The HtppKernel Component:The Controller Resolver](http://symfony.com/doc/3.2/create_framework/http_kernel_controller_resolver.html)

### The Separation Of Concerns

我们的目标是构建一个框架，前面的代码虽然可满足要求，但是缺少封装，没有放到命名空间，这个在规模扩大时并不方便。同时每建一个新站都需要复制整个`front.php`。对它们做封装可提高可用性和可测试性。

本节引入了`命名空间`，创建`Simplex\Framework`的类和`控制器`以及增加`psr-4`的自动加载。

本节的分离关注的意义其实是从工程层面体现的：通过对前面实现的功能做一次代码整理，揭示`现代WEB PHP框架`的基本目录组织方法。

[原文：The Separation Of Concerns](http://symfony.com/doc/3.2/create_framework/separation_of_concerns.html)


### Unit Testing
这一节，对于`Framework`这个的类测试了`404`, `500`和`正常响应`，该类的测试覆盖率为`100%`。这一节对于后续学习单元测试是很有启发性的：

- 如何配置单元测试文件`phpunit.xml.dist`
- 如何创建`Mock Object`，以避免要依赖真实环境；
- 如何尽可能的覆盖测试，通过`404`， `500`，`正常响应`的示例说明；
- 如何生成覆盖率报告:

```
$phpunit --coverage-text # 命令行输出
$phpunit --coverage-html=cov/ # 输出HTML文档
```

这一节的启发在于：在写代码时，传参应该尽量设计成接口才方便`Mock`；而错误以`throw`的方式抛出；这样子会方便测试。另外如果你能从单元测试的角度去考虑框架，就会发现很多框架中觉得可能多余的设计并不是多余的。比如`Laravel`的`Facade`。
[原文：Unit Testing](http://symfony.com/doc/3.2/create_framework/unit_testing.html)

### event dispatcher
整个框架虽然是完备的，但称不上是一个好框架。所有的好框架都有很强的可扩展性。那么什么是可扩展性呢，作者给了一个蛮不错的定义：

> Being extensible means that the developer should be able to easily hook into the framework life cycle to modify the way the request is handled.

实际上，`event dispatcher`这个名字不好理解，我是直接把它当成`Laravel`的`middleware`来看待。

[原文：event dispatcher](http://symfony.com/doc/current/create_framework/event_dispatcher.html)

### The HttpKernel Component: HttpKernelInterface

`HttpKernelInterface`是`HttpKernel`组件最重要的一个方法。许多组件都依赖于该接口，比如`HttpCache`。所以自己设计框架的时候，应该实现该接口，以便更好地利用现有组件。（这一节跟下面一节总结起来呢就是一句话：自己实现的框架核心会有很多问题，还是使用`HttpKernel`这个组件好)

[原文：The HttpKernel Component: HttpKernelInterface](http://symfony.com/doc/current/create_framework/http_kernel_httpkernelinterface.html)

### The HttpKernel Component: The HttpKernel Class
`HttpKernel`是`HttpKernelInterface`的默认实现。相比于自己实现，它提供了更完备的处理机制，比如我们自己的框架只处理了`404`和`500`的错误，但还有其他的错误没处理；另外，它提供了`event dispatcher`的各种默认机制，允许灵活地控制异常时、控制器进入前后、渲染视图时的显示；最后，在安全方面和规模增长后的表现也在各个实际的网站中表现得十分优异。
[原文：The HttpKernel Component: The HttpKernel Class](http://symfony.com/doc/current/create_framework/http_kernel_httpkernel_class.html)

### The DependencyInjection Comonent
`front.php`的代码基本上在每个应用中都是重复的，可以考虑将其移到`Framework`的`构造函数`中，但是你会发现：没法添加新的`listener`, 没办法模拟接口做单元测试等等。在实际场景中，我们需要区分开发环境与生产环境；或者想要添加越来越多的`dispatcher`；改变`response`的输出字符集等，由于相关的类都只在`front.php`中出现，所以这些改动都要在`front.php`中增加代码完成，最终显然会导致`front.php`越来越大。而当我们搞一个新的应用时又需要将`front.php`拷贝过去，万一要改时就显得更不方便。有没有一个好的方法，能够保持依然当前框架的灵活性，但是又要可定制，可以单元测试，同时又没有重复代码吗？依赖注入（DI）就是解决这个问题的好方法。`symfony/dependency-injection`就是一个棒的`DI`组件，另外一个轻量级`Pimple`也是广受好评。


通过依赖注入，不同的服务都变成了可配置的。框架本身也通过容器初始化，初始化时的参数也都是容器，可根据需要传递不同的实现。而`disptacher`也是个容器，配置的时候可以根据实际情况在初始化阶段添加尽可能多的`listener`。最终，`front.php`的代码就变成获取`framework`的容器即可，其他的事情则在`container.php`配置。当程序变复杂时，将`listener`单独独立出来，将配置单独独立出来，都是很简单的事情。基本上可以说，依赖注入是现代框架的标配了。

[原文：The DependencyInjection Comonent](http://symfony.com/doc/current/create_framework/dependency_injection.html)

### END