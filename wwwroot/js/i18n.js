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

	function formatLocale(str) {
		return str.toLowerCase().replace('-', '_');
	}

	function fGetLocaleMap(aLocales) {
		const mRet = {};
		for (const mLocaleOne of aLocales) {
			mRet[mLocaleOne.name] = mLocaleOne;
		}
		return mRet;
	}

	function fGetLangMap(mLocaleMap) {
		const mRet = {};
		for (const sLocale in mLocaleMap) {
			const sLang = sLocale.split('_')[0];
			mRet[sLang] = mLocaleMap[sLocale];
		}
		return mRet;
	}

	const mConfLocale = fGetLocaleMap(i18n_locales);

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
		if (sFormatPath === null) {
			return sFormatPath;
		}
		if (typeof (sFormatPath) !== 'string') {
			// 只处理[数据类型]为[字符串]，其他类型直接返回，例如：数字型和逻辑型
			return sFormatPath;
		}
		const locale = fGetCurrentLocale(mCacheData);
		if (!locale) {
			// 没有匹配到语言包
			return sFormatPath;
		}
		const aFormatPath = sFormatPath.split('.');
		const i18nDataByGroupName = mCacheData[locale];
		if (!i18nDataByGroupName.hasOwnProperty(aFormatPath[0])) {
			// 匹配不到语言组的情况下，直接对字符串进行处理
			return fGetFormatString(sFormatPath, aParam);
		}
		const i18nDataByFormatKey = i18nDataByGroupName[aFormatPath[0]];
		if (!i18nDataByFormatKey.hasOwnProperty(aFormatPath[1])) {
			// 匹配不到语言项的情况下，直接对字符串进行处理
			return fGetFormatString(sFormatPath, aParam);
		}
		const sFormatValue = i18nDataByFormatKey[aFormatPath[1]];
		return fGetFormatString(sFormatValue, aParam);
	}

	function fGetCurrentLocale(mLocale) {
		if (mLocale === undefined) {
			mLocale = mConfLocale;
		}
		// locale = lang code(iso639) + country code(iso3166)
		const locale = oI18nState.get('locale');
		if (locale && mLocale.hasOwnProperty(locale)) {
			return locale;
		}
		const navLang = navigator.language || navigator.userLanguage;
		// 1：首先尝试匹配完整的locale
		const LocaleFormatted = formatLocale(navLang);
		if (mLocale.hasOwnProperty(LocaleFormatted)) {
			return LocaleFormatted;
		}
		const locale0 = navLang.split('-')[0];
		// 2：如果完整的匹配不到的情况下可以只匹配lang
		const mLangMap = fGetLangMap(mLocale);
		if (mLangMap.hasOwnProperty(locale0)) {
			return mLangMap[locale0].name;
		}
		// 3：都匹配不到的情况下随便选一个
		for (const k in mLocale) {
			return k;
		}
	}

	const { defineStore } = Pinia;
	return defineStore('i18n', {
		state: () => {
			return {
				locale: fGetCurrentLocale(mCacheData),
				mConfLocale: mConfLocale,
			}
		},
		actions: {
			fGetTransResult,
			// fSetCurrentLocale(val) {
			// 	// 设置语言
			// 	this.locale = val;
			// 	oI18nState.set('locale', val);
			// 	oI18nState.save();
			// },
			fSetCurrentLocale(val) {
				// 设置语言
				this.locale = val;
				oI18nState.set('locale', val);
				oI18nState.save();
			},
			fRemoveCurrentLocale() {
				// 恢复默认语言
				oI18nState.del('locale');
				oI18nState.save();
			},
			fGetLocales() {
				return i18n_locales;
			},
		}
	});
});
