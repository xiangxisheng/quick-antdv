function stringifyQuery(queryObj) {
  if (!queryObj) {
    return '';
  }

  const pairs = [];
  Object.keys(queryObj).forEach(key => {
    const value = queryObj[key];
    if (Array.isArray(value)) {
      value.forEach(item => {
        pairs.push(`${encodeURIComponent(key)}=${encodeURIComponent(item)}`);
      });
    } else {
      pairs.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
    }
  });

  return pairs.join('&');
};

async function fetchDataByPathname(_pathname, _param) {
  const url = `${_pathname}?` + stringifyQuery(_param);
  try {
    const oResponse = await fetch(url);
    if (oResponse.status !== 200) {
      return { message: oResponse.statusText };
    }
    return await oResponse.json();
  } catch (e) {
    return e;
  }
};

async function P(name) {
  return (await import(`../page/${name}.js`)).default;
};
export { fetchDataByPathname, P };
