window.i18n = (() => {

    const oI18nState = firadio.stateStorage('i18n');

    function fGetLocaleMap(mConfLocale, mConfLang) {
        for (const iIndex in window.config.i18n.locales) {
            const mLocale = window.config.i18n.locales[iIndex];
            mConfLocale[mLocale.locale] = iIndex;
            const sLang = mLocale.locale.split('-')[0];
            mConfLang[sLang] = mLocale.locale;
        }
    }
    const mConfLocale = {};
    const mConfLang = {};
    fGetLocaleMap(mConfLocale, mConfLang);

    function fGetLocales() {
        return window.config.i18n.locales;
    }

    function replaceAll(s0, s1, s2) {
        return s0.replace(new RegExp(s1, "gm"), s2);
    }

    function fGetFormatString(sFormatValue, aParam) {
        if (aParam) {
            var out = sFormatValue;
            for (const k in aParam) {
                console.log(out);
                out = replaceAll(out, '\\{' + k + '\\}', aParam[k]);
            }
            return out;
        }
        return sFormatValue;
    }

    function fGetTransResult(sFormatPath, aParam) {
        const locale = fGetCurrentLocale();
        if (!locale) {
            return '-';
        }
        if (!mConfLocale.hasOwnProperty(locale)) {
            return '-';
        }
        const aFormatPath = sFormatPath.split('.');
        const i18nDataByGroupName = window.config.i18n.data;
        const iLocaleIndex = mConfLocale[locale];
        if (!i18nDataByGroupName.hasOwnProperty(aFormatPath[0])) {
            return sFormatPath;
        }
        const i18nDataByFormatKey = i18nDataByGroupName[aFormatPath[0]];
        if (!i18nDataByFormatKey.hasOwnProperty(aFormatPath[1])) {
            return sFormatPath;
        }
        const aFormatValueByLocaleIndex = i18nDataByFormatKey[aFormatPath[1]];
        const sFormatValue = aFormatValueByLocaleIndex[iLocaleIndex];
        return fGetFormatString(sFormatValue, aParam);
    }

    function fGetCurrentLocale() {
        // locale = lang code(iso639) + country code(iso3166)
        const locale = oI18nState.get('locale');
        if (locale && mConfLocale.hasOwnProperty(locale)) {
            return locale;
        }
        var navLang = navigator.language || navigator.userLanguage;
        if (mConfLocale.hasOwnProperty(navLang)) {
            return navLang;
        }
        const locale0 = navLang.split('-')[0];
        if (mConfLang.hasOwnProperty(locale0)) {
            return mConfLang[locale0];
        }
        for (const k in mConfLocale) {
            return k;
        }
    }

    function fSetCurrentLocale(val) {
        // 设置语言
        oI18nState.set('locale', val);
    }

    function fRemoveCurrentLocale() {
        // 恢复默认语言
        oI18nState.del('locale');
    }

    function fLoad() {
        // 为tabBar重新加载语言
        if (0 && pages.globalStyle) {
            if (pages.globalStyle.navigationBarTitleText) {
                const title = fGetTransResult(pages.globalStyle.navigationBarTitleText);
                pages.globalStyle.navigationBarTitleText = title;
            }
        }
        if (pages.tabBar && pages.tabBar.list) {
            for (const index in pages.tabBar.list) {
                const item = pages.tabBar.list[index];
                const tabBarOptions = {
                    index: parseFloat(index),
                    text: fGetTransResult(item.text),
                }
                uni.setTabBarItem(tabBarOptions);
            }
        }
    }

    const i18n = {};
    i18n.fGetLocales = fGetLocales;
    i18n.fGetTransResult = fGetTransResult;
    i18n.fGetCurrentLocale = fGetCurrentLocale;
    i18n.fSetCurrentLocale = fSetCurrentLocale;
    i18n.fRemoveCurrentLocale = fRemoveCurrentLocale;
    i18n.fGetLocales = fGetLocales;
    return i18n;
});
