import React, { createElement } from 'react';
import classNames from 'classnames';
import { Button } from 'antd';
import config from './scTypes';
import styles from './index.module.scss';

export interface ExceptionProps {
  type?: '403' | '404' | '500';
  title?: React.ReactNode;
  desc?: React.ReactNode;
  img?: string;
  actions?: React.ReactNode;
  linkElement?: string | React.ComponentType;
  style?: React.CSSProperties;
  className?: string;
  backText?: React.ReactNode;
  redirect?: string;
}

export default class Exception extends React.PureComponent<ExceptionProps> {
  static defaultProps = {
    backText: 'back to home',
    redirect: '/'
  };

  constructor(props: ExceptionProps) {
    super(props);
    this.state = {};
  }

  render() {
    const {
      className,
      backText,
      linkElement = 'a',
      type,
      title,
      desc,
      img,
      actions,
      redirect,
      ...rest
    } = this.props;

    // 判断没传type或者type值不存在的话，默认404
    const pageType = type ?
      type in config ?
        type :
        '404' :
      '404';
    const clsString = classNames(styles.exception, className);
    return (
      <div className={clsString} {...rest}>
        <div className={styles.imgBlock}>
          <div
            className={styles.imgEle}
            style={{ backgroundImage: `url(${img || config[pageType].img})` }}
          />
        </div>
        <div className={styles.content}>
          <h1>{title || config[pageType].title}</h1>
          <div className={styles.desc}>{desc || config[pageType].desc}</div>
          <div className={styles.actions}>
            {actions ||
              createElement(
                linkElement,
                {
                  to: redirect,
                  href: redirect,
                } as any,
                <Button type="primary">{backText}</Button>
              )}
          </div>
        </div>
      </div>
    );
  }
}