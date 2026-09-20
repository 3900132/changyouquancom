# 常友圈 · 表情密文翻译器官网

> Emoji Cipher Translator — 一个将文本与表情包双向加密转换的趣味工具，这是它的官方网站。

🔗 **在线体验：[https://changyouquan.com](https://changyouquan.com)**

[简体中文](#简介) | [English](#english)

---

## 简介

「常友圈」是一个 **表情密文翻译器**：可以把普通文本转换为一串表情符号（密文），也能将表情密文还原为原文，并支持自定义专属密钥。无需注册登录，所有转换在浏览器本地完成。

本仓库是该工具的**官方网站源码**，一个零依赖、纯静态的 HTML/CSS/JS 站点。

### ✨ 特性

- 🔒 **文本 ↔ 表情密文** 双向转换，支持专属密钥
- 🌍 **多语言支持**：简体中文、繁体中文、英语、西班牙语、法语、德语、日语、韩语（8 种语言）
- 🌗 **明暗主题** 一键切换
- 📱 **响应式设计**，适配桌面与移动端
- 🚀 **零构建、零依赖**：无需 Node/npm，直接部署静态文件即可
- 🔀 基于 EdgeOne Pages 路由的多语言 URL（如 `/zh/`、`/en/`、`/ja/`）

### 📁 目录结构

```
.
├── index.html          # 首页（翻译器主界面）
├── help.html           # 使用帮助
├── about.html          # 关于我们
├── 404.html            # 404 页面
├── help.php            # PHP 版帮助页（可选）
├── edgeone.json        # EdgeOne Pages 路由/重写配置
├── i18n/               # 国际化脚本
│   ├── i18n.js
│   └── locales/        # 各语言翻译文件 (zh / zh-TW / en / es / fr / de / ja / ko)
├── fontawesome6.4/     # 内置的 Font Awesome 6.4 静态资源
└── logo.png            # 站点 Logo
```

## 🚀 快速开始

本项目为纯静态站点，无需安装依赖、无需构建。

### 本地运行

任选一种方式启动静态服务器：

```bash
# Python
python -m http.server 8080

# Node.js
npx serve .

# PHP
php -S localhost:8080
```

然后访问 `http://localhost:8080`。

### 部署到 EdgeOne Pages

1. 将本仓库导入 [腾讯云 EdgeOne Pages](https://edgeone.ai/products/pages)；
2. 无需配置构建命令，输出目录为仓库根目录；
3. 仓库中的 `edgeone.json` 已包含多语言路由重写规则（`/zh/` → `/index.html` 等），部署后自动生效。

### 部署到其他平台

同样适用于 GitHub Pages、Vercel、Netlify、Cloudflare Pages 等任何静态托管服务。若需保留 `/zh/`、`/en/` 等多语言路径，请参考 `edgeone.json` 在对应平台上配置等价的重写规则。

## 🤝 参与贡献

欢迎提交 Issue 和 Pull Request！

- 翻译改进：编辑 `i18n/locales/*.json`
- 样式 / 功能：修改对应 HTML 与 CSS
- 提交前请确保页面在主流浏览器中正常工作

## 📄 许可证

本项目采用 [MIT License](LICENSE) 开源。

## 📮 联系我们

如有建议或合作意向，欢迎联系：webmaster#3520.net（请将 `#` 替换为 `@`）

---

<a id="english"></a>
## English

**Changyouquan · Emoji Cipher Translator** — the official website of a fun tool that converts plain text into emoji ciphers and back, with support for custom secret keys. No sign-up required; everything runs locally in your browser.

This repository contains the **source code of the official website**: a zero-dependency, fully static HTML/CSS/JS site.

🔗 **Live demo: [https://changyouquan.com](https://changyouquan.com)**

### ✨ Features

- 🔒 Two-way **text ↔ emoji cipher** conversion with custom keys
- 🌍 **8 languages**: Simplified/Traditional Chinese, English, Spanish, French, German, Japanese, Korean
- 🌗 Light/dark theme toggle
- 📱 Responsive design for desktop and mobile
- 🚀 **Zero build, zero dependencies** — deploy the static files as-is
- 🔀 Multi-language URLs via EdgeOne Pages rewrites (`/zh/`, `/en/`, `/ja/`, …)

### 🚀 Quick Start

No installation or build step required. Serve the folder with any static server:

```bash
python -m http.server 8080
# or
npx serve .
```

Then open `http://localhost:8080`.

### Deployment

Works out of the box on **Tencent EdgeOne Pages** (routing rules are in `edgeone.json`), and equally well on GitHub Pages, Vercel, Netlify, or Cloudflare Pages.

### Contributing & License

Issues and PRs are welcome — translations live in `i18n/locales/*.json`. Released under the [MIT License](LICENSE).
