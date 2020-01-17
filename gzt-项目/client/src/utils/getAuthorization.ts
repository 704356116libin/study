let $hasLogged = true;
let $Authorization: string = 'Unauthenticated';
if (process.env.NODE_ENV === 'development') {// 如果是开发期间
  if (process.env.REACT_APP_ACCESS_TOKEN) {// 前后端分离开发期间
    $Authorization = `Bearer ${process.env.REACT_APP_ACCESS_TOKEN}`
  } else {
    alert('请手动配置env文件，REACT_APP_ACCESS_TOKEN=你的access_token');
    $hasLogged = false
  }
} else {// 服务端渲染
  if (localStorage.getItem('access_token')) {
    $Authorization = `Bearer ${localStorage.getItem('access_token')}`
  } else { // 进入页面首次判断是否登录
    $hasLogged = false;
  }
}

/**
 * 是否登录
 */
const hasLogged = $hasLogged;
const Authorization = $Authorization;

export { hasLogged, Authorization }