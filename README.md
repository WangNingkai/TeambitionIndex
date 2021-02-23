# Teambition-Index

✨ Another Teambition Directory Index.

## 简介

一款 基于`Teambition` 目录文件索引应用，简单而强大。

## 功能

- Teambition 文件目录索引
- 支持多种资源即时预览
- 直链分享

## 构建

自行构建前需要拥有 Go >= 1.16、yarn 等必要依赖。

### 克隆代码

```bash
git clone  https://github.com/wangningkai/teambition-index.git
```

### 构建静态资源

```bash
# 进入前端子模块
cd assets
# 安装依赖
yarn
# 开始构建
yarn run build
```

### 嵌入静态资源

```bash
# 回到项目主目录
cd ../

# 安装 statik, 用于嵌入静态资源
go get github.com/rakyll/statik

# 开始嵌入
statik -f -src=assets/dist/ -include=*.html,*.js,*.json,*.css,*.png,*.svg,*.ico,*.woff2,*.woff
```

### 编译项目

```bash
go build -o teambition -ldflags "-w -s"
```

你也可以使用项目根目录下的 build.sh 快速开始构建：

```bash
./build.sh  [-a] [-c] [-b] [-r]
	a - 构建静态资源
	c - 编译二进制文件
	b - 构建前端 + 编译二进制文件
	r - 交叉编译，构建用于release的版本
```

## License

The Teambition-Index is open-source software licensed under the MIT license.
