# PhQAgent
PHP开发的WebQQ协议支持插件的机器人框架

## 安装方法
### Windows
 - 下载Windows二进制执行环境
 - [PhQAgent/Binary/win32](https://github.com/PhQAgent/Binary/blob/master/win32.zip?raw=true)
 - 下载PhQAgent打包
 - [PhQAgent/Release/PhQAgent.phar](https://github.com/PhQAgent/Release/blob/master/PhQAgent.phar?raw=true)
 - 将phar打包放入解压好的二进制执行环境后，运行目录下的start.cmd即可启动
### Linux
 - 在Shell下执行
 - wget -q -O - https://raw.githubusercontent.com/PhQAgent/Installer/master/install.sh | bash
 - 即可安装

## 安装插件
 - 程序第一次运行后将会创建plugins文件夹，将插件放入其中即可在下次启动时加载
 - 看一下 [Plugin Repo](https://github.com/PhQAgent/Plugin) 下的示例插件，我相信你会有更多更好的创意的
 - 如果你曾经开发过PocketMine插件，一定是很容易就能上手的
