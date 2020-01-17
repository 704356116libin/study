import React, { useEffect, useState } from 'react';
import { Icon, Avatar } from 'antd'
import pA from './index.module.scss'

export interface PersonnelAvatarProps {
  avatarText?: string;
  close?: boolean;
  name?: string;
  src?: string;
  onClose?: (e: React.MouseEvent<HTMLDivElement, MouseEvent>) => void;
  onClick?: (e: React.MouseEvent<HTMLDivElement, MouseEvent>) => void;
}

const colorList = ['#f56a00', '#7265e6', '#ffbf00', '#00a2ae'];

/**
 * 展示审批、抄送人员 头像
 */
export default function PersonnelAvatar({
  avatarText,
  close = true,
  name = '用户',
  src,
  onClose,
  onClick
}: PersonnelAvatarProps) {

  const [randomNumber, setRandomNumber] = useState(0);

  useEffect(() => {

    setRandomNumber(Math.floor(Math.random() * 4))

  }, [])

  function $close(e: React.MouseEvent<HTMLDivElement, MouseEvent>) {
    e.stopPropagation();
    onClose && onClose(e);
  }

  // 有src的话展示图片
  const avatar = src ? (
    <Avatar
      className={pA.Avatar}
      size={42}
      src={src}
      style={{ backgroundColor: colorList[randomNumber] }}
    />
  ) :
    avatarText ? (// 没有的话展示传进来的头像文字的后两个字
      <Avatar
        className={pA.Avatar}
        size={42}
        style={{ fontSize: '14px', backgroundColor: colorList[randomNumber] }}
      >
        {avatarText.substr(avatarText.length - 2, avatarText.length)}
      </Avatar>
    ) :
      (// 也没有的话展示用户名字的后两个字
        <Avatar
          className={pA.Avatar}
          size={42}
          style={{ fontSize: '14px', backgroundColor: colorList[randomNumber] }}
        >
          {name.substr(name.length - 2, name.length)}
        </Avatar>
      )

  return (
    <div
      className={pA.Wrapper}
      onClick={onClick}
    >
      {
        close
          ? <Icon className={pA.Close} type="close" onClick={$close} />
          : null
      }
      {avatar}
      <div className={pA.Name} title={name}>
        {name}
      </div>
    </div>
  )
}