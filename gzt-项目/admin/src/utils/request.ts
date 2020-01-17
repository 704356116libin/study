let $hasLogged = true;
let Authorization: string;
if (process.env.NODE_ENV === 'development') {// 如果是开发期间
  if (process.env.REACT_APP_ACCESS_TOKEN) {// 前后端分离开发期间
    Authorization = `Bearer ${process.env.REACT_APP_ACCESS_TOKEN}`
  } else {
    alert('请手动配置env文件，REACT_APP_ACCESS_TOKEN=你的access_token');
    $hasLogged = false
  }
} else {// 服务端渲染
  if (localStorage.getItem('access_token')) {
    Authorization = `Bearer ${localStorage.getItem('access_token')}`
  } else { // 进入页面首次判断是否登录
    $hasLogged = false;
  }
}

/**
 * 是否登录
 */
export const hasLogged = $hasLogged;

function checkStatus(response: Response) {
  if (response.status >= 200 && response.status < 300) {
    return response;
  }
  if (response.status === 500) {
    // 此处是异步操作，即使能判断登录失效，跳转到登录页，还是会先抛出错误
    // 解决办法 服务端直接返回代表登录失效（未经授权）状态码，比如401
    response.json().then((json) => {
      if (json.message === 'Unauthenticated.') {// 如果未登录
        if (process.env.NODE_ENV === 'development') {// 如果是开发期间
          window.location.href = `http://${process.env.REACT_APP_PROXY_TARGET}/login`
        } else {
          window.location.href = '/login'
        }
        return
      }
    })
  }
  const error = new Error(response.statusText);
  // error.response = response;
  throw error;
}
/**
 * get 请求
 * @param url 
 * @param params 
 */
export async function get(url: string, options?: any) {
  if (options) {
    if (options.method === "GET" || !options.method) {
      if (options.params) {
        const params = options.params;
        const paramsArray: string[] = [];
        // 拼接参数
        Object.keys(params).forEach(key => paramsArray.push(key + '=' + params[key]))
        if (url.search(/\?/) === -1) {
          url += '?' + paramsArray.join('&')
        } else {
          url += '&' + paramsArray.join('&')
        }
      }
    }
    options.headers = {
      Authorization
    }
  } else {
    options = {
      headers: {
        Authorization
      }
    }
  }
  const response = await fetch(url, options);
  let resToJson = (options && options.resToJson === false) ? false : true;
  checkStatus(response);
  return await resToJson ? response.json() : response.text();
}
/**
 * 基础请求
 * @param url 
 * @param options 
 */
export default async function request(url: string, options?: any) {
  let getFile = false; // 是否请求文件
  if (options) {
    if (options.headers) {
      if (!options.headers['Content-Type']) {
        options.headers['Content-Type'] = "application/json";
      }
      options.headers.Authorization = Authorization;
    } else {
      const headers = {
        "Content-Type": "application/json",
        Authorization
      }
      options.headers = headers;
    }

    if (options.body) {
      const body = options.body;
      options.body = JSON.stringify(body);
    }

    if (options.getFile === true) {// 判断是否下载文件
      getFile = true;
    }
  } else {
    options = {
      headers: {
        Authorization
      }
    }
  }

  const response = await fetch(url, options);
  checkStatus(response);
  if (getFile) {
    return {
      headers: response.headers,
      blob: await response.blob()
    }
  }
  return await response.json();
}