window.i18n = (() => {

    const oI18nState = firadio.stateStorage('i18n');
    const mCacheData = {};
    async function fGetI18nData(locale) {
        const jsonPath = `data/lang/${locale}.json`;
        mCacheData[locale] = await (await fetch(jsonPath)).json();
    }
    fGetI18nData('en_us');
    fGetI18nData('zh_cn');

    const i18n_locales = [
        {
            "name": "en_us",
            "title": "English"
        },
        {
            "name": "zh_cn",
            "title": "简体中文"
        },
        {
            "name": "km_kh",
            "title": "ខ្មែរ"
        }
    ];

    function fGetLocaleMap(mConfLocale, mConfLang) {
        for (const iIndex in i18n_locales) {
            const mLocale = i18n_locales[iIndex];
            mConfLocale[mLocale.name] = iIndex;
            const sLang = mLocale.name.split('_')[0];
            mConfLang[sLang] = mLocale.name;
        }
    }
    const mConfLocale = {};
    const mConfLang = {};
    fGetLocaleMap(mConfLocale, mConfLang);

    function fGetLocales() {
        return i18n_locales;
    }

    function replaceAll(s0, s1, s2) {
        return s0.replace(new RegExp(s1, "gm"), s2);
    }

    function fGetFormatString(sFormatValue, aParam) {
        if (aParam) {
            var out = sFormatValue;
            for (const k in aParam) {
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
        if (sFormatPath === null) {
            return sFormatPath;
        }
        if (typeof (sFormatPath) === 'number') {
            return sFormatPath;
        }
        const aFormatPath = sFormatPath.split('.');
        const i18nDataByGroupName = mCacheData[locale];
        const iLocaleIndex = mConfLocale[locale];
        if (!i18nDataByGroupName.hasOwnProperty(aFormatPath[0])) {
            return fGetFormatString(sFormatPath, aParam);
        }
        const i18nDataByFormatKey = i18nDataByGroupName[aFormatPath[0]];
        if (!i18nDataByFormatKey.hasOwnProperty(aFormatPath[1])) {
            return fGetFormatString(sFormatPath, aParam);
        }
        const sFormatValue = i18nDataByFormatKey[aFormatPath[1]];
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

    const i18n = {};
    i18n.fGetLocales = fGetLocales;
    i18n.fGetTransResult = fGetTransResult;
    i18n.fGetCurrentLocale = fGetCurrentLocale;
    i18n.fSetCurrentLocale = fSetCurrentLocale;
    i18n.fRemoveCurrentLocale = fRemoveCurrentLocale;
    return i18n;
})();
