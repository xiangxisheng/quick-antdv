@ECHO OFF
TITLE %0
mkdir css

powershell -c "Invoke-RestMethod -Uri \"https://github.com/atisawd/boxicons/raw/master/css/boxicons.min.css\" -OutFile \"css/boxicons.min.css\""
powershell -c "Invoke-RestMethod -Uri \"https://cdn.jsdelivr.net/npm/ant-design-vue/dist/reset.min.css\" -OutFile \"css/reset.min.css\""


mkdir fonts
powershell -c "Invoke-RestMethod -Uri \"https://github.com/atisawd/boxicons/raw/master/fonts/boxicons.woff2\" -OutFile \"fonts/boxicons.woff2\""

mkdir img
powershell -c "Invoke-RestMethod -Uri \"https://next.antdv.com/assets/logo.1ef800a8.svg\" -OutFile \"img/logo.svg\""

mkdir js
mkdir js\antd
powershell -c "Invoke-RestMethod -Uri \"https://cdn.jsdelivr.net/npm/ant-design-vue/dist/antd.min.js\" -OutFile \"js/antd/antd.min.js\""
powershell -c "Invoke-RestMethod -Uri \"https://cdn.jsdelivr.net/npm/ant-design-vue/dist/antd.min.js.map\" -OutFile \"js/antd/antd.min.js.map\""
powershell -c "Invoke-RestMethod -Uri \"https://cdn.jsdelivr.net/npm/dayjs/dayjs.min.js\" -OutFile \"js/antd/dayjs.min.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/customParseFormat.js\" -OutFile \"tmp-1.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/weekday.js\" -OutFile \"tmp-2.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/localeData.js\" -OutFile \"tmp-3.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/weekOfYear.js\" -OutFile \"tmp-4.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/weekYear.js\" -OutFile \"tmp-5.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/advancedFormat.js\" -OutFile \"tmp-6.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/dayjs/plugin/quarterOfYear.js\" -OutFile \"tmp-7.js\""
DEL js\antd\dayjs-plugin.min.js
type tmp-1.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-2.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-3.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-4.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-5.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-6.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
type tmp-7.js 1>>js\antd\dayjs-plugin.min.js
echo. 1>>js\antd\dayjs-plugin.min.js
DEL tmp-*.js

mkdir js\sheetjs
powershell -c "Invoke-RestMethod -Uri \"https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js\" -OutFile \"js/sheetjs/xlsx.full.min.js\""

mkdir js\vue
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/pinia/dist/pinia.iife.prod.js\" -OutFile \"js/vue/pinia.iife.prod.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/vue/dist/vue.global.prod.js\" -OutFile \"js/vue/vue.global.prod.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/vue-demi/lib/index.iife.js\" -OutFile \"js/vue/vue-demi.iife.js\""
powershell -c "Invoke-RestMethod -Uri \"https://unpkg.com/vue-router/dist/vue-router.global.prod.js\" -OutFile \"js/vue/vue-router.global.prod.js\""

echo The static directory has been downloaded successfully!
PAUSE
