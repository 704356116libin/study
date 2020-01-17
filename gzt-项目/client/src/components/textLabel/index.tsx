import React from 'react'
export default function TextLabel({
  text,
  colon = true,
  ...rest
}: {
  text: string;
  colon?: boolean;
  [propName: string]: any;
}) {
  return (
    <span className="text-label" {...rest}>{text}{colon ? '：' : ''}</span>
  )
}