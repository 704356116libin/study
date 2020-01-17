import * as React from 'react';
import { RouterAPI } from 'dva'; // 引入接口声明
import { Spin } from 'antd';
import { Router, Route, Redirect } from 'react-router-dom';
import CacheRoute, { CacheSwitch } from 'react-router-cache-route';
import { routes } from '../route';
import App from '../App';
import Err404 from '../pages/error/404'; // 直接引入

export default function RouterConfig(router?: RouterAPI) {
  if (router) {
    return (
      <Router history={router.history}>
        <App>
          <CacheSwitch>
            {searchRoute(routes)}
            <Route path="/" component={Err404} />
          </CacheSwitch>
        </App>
      </Router>
    )
  }
  return Object
}

/**
 * 封装react路由, 传入一个配置数组, 输出路由
 * @param routes 自定义的路由配置数组
 * @param matchs 路由参数
 */
export function searchRoute(routes: any[], matchs?: any) {
  return routes.map((route: any) => {
    if (route.redirect) { // 重定向
      return (
        <Route
          key={route.path}
          path={matchs ? matchs.url + route.path : route.path}
          exact={route.path === '/'}
          render={() => (
            <Redirect
              to={matchs ? matchs.url + route.redirect : route.redirect}
            />
          )}
        />
      )
    } else { // 递归处理路由

      // 是否需要缓存路由
      const FRoute = route.cache ? CacheRoute : Route;
      const path = matchs ? matchs.url + route.path : route.path;

      const cacheProps = route.cache ? {
        cacheKey: path,
        behavior: (cached: boolean) => (
          cached
            ? {
              style: {
                display: 'none'
              }
            }
            : {
              className: 'ant-layout'
            }
        )
      } : null
      /**   
       *  delay={200} 延迟有bug,暂时不能用
       */
      return (
        <FRoute
          exact={route.path === '/' || route.exact === true}
          key={route.path}
          {...cacheProps}
          path={path}
          render={(props: any) => (
            <React.Suspense fallback={<Spin size="large" />}>
              <route.component {...props}>
                {route.routes ? searchRoute(route.routes, props.match) : null}
              </route.component>
            </React.Suspense>
          )}
        />
      )
    }
  })
}
