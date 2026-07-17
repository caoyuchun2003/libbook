<template>
  <router-view />
</template>

<script setup>
</script>

<style>
:root {
  --yc-ink: #163a3c;
  --yc-ink-soft: #2a5558;
  --yc-paper: #eef2f1;
  --yc-surface: #f7faf9;
  --yc-accent: #c4783a;
  --yc-text: #1c2b2c;
  --yc-muted: #5f7374;
  --yc-line: rgba(22, 58, 60, 0.12);
  --yc-font-display: "Noto Serif SC", "Songti SC", serif;
  --yc-font-body: "DM Sans", "PingFang SC", "Hiragino Sans GB", sans-serif;
  --el-color-primary: #163a3c;
  --el-color-primary-light-3: #2a5558;
  --el-color-primary-light-5: #4a7275;
  --el-color-primary-light-7: #8aa5a7;
  --el-color-primary-light-8: #b3c5c6;
  --el-color-primary-light-9: #d9e3e4;
  --el-color-primary-dark-2: #0f2a2c;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body,
#app {
  min-height: 100%;
}

body {
  font-family: var(--yc-font-body);
  color: var(--yc-text);
  background: var(--yc-paper);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

h1, h2, h3, .yc-display {
  font-family: var(--yc-font-display);
  font-weight: 600;
  letter-spacing: 0.02em;
}

a {
  color: var(--yc-ink-soft);
}
</style>
