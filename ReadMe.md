# Tailwind CSS Setup Guide (HTML & PHP)

This guide walks you through configuring **Tailwind CSS v3** for a project that uses **HTML and PHP** files.

---

## 📦 Prerequisites

Make sure you have the following installed:

- **Node.js** (v16+ recommended)
- **npm** (comes with Node.js)

Check installation:
```bash
node -v
npm -v
````

---

## 🚀 Step 1: Initialize npm

Create a `package.json` file for your project:

```bash
npm init -y
```

---

## 📥 Step 2: Install Tailwind CSS

Install Tailwind CSS as a development dependency:

```bash
npm install -D tailwindcss@3
```

---

## ⚙️ Step 3: Initialize Tailwind Config

Generate the Tailwind configuration file:

```bash
npx tailwindcss init
```

This will create:

```text
tailwind.config.js
```

---

## 🛠 Step 4: Configure Tailwind Content Paths

Edit `tailwind.config.js` so Tailwind knows where to scan for class names:

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    // Optional: specify folders if needed
    // "./src/**/*.php",
    // "./views/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

✅ This ensures Tailwind works properly with **PHP and HTML files**.

---

## 🎨 Step 5: Create Your CSS Input File

Create a folder and CSS file:

```text
src/input.css
```

Add the Tailwind directives:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

---

## 🧾 Step 6: Add Build Scripts

Open `package.json` and add the following scripts:

```json
{
  "scripts": {
    "build": "tailwindcss -i ./src/input.css -o ./dist/output.css",
    "watch": "tailwindcss -i ./src/input.css -o ./dist/output.css --watch"
  }
}
```

📁 This will output the compiled CSS to:

```text
dist/output.css
```

---

## 🏗 Step 7: Build Tailwind CSS

Run once:

```bash
npm run build
```

Or during development (auto rebuild):

```bash
npm run watch
```

---

## 🔗 Step 8: Link Tailwind to Your HTML / PHP

Include the generated CSS file in your HTML or PHP layout:

```html
<link rel="stylesheet" href="./dist/output.css">
```

Example in PHP:

```php
<link rel="stylesheet" href="../dist/output.css">
```

---

## ✅ Done!

You can now use Tailwind CSS utility classes in your HTML and PHP files 🎉

Example:

```html
<div class="bg-blue-500 text-white p-4 rounded">
  Hello Tailwind!
</div>
```

---

## 📌 Notes

* Always run `npm run watch` while developing
* Make sure your PHP/HTML files are included in `content[]`
* Tailwind only generates styles **that are actually used**
