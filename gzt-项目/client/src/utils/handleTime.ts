import moment from "moment";

/**
 * 处理服务端返回的2019-01-18 15:36:59格式的时间为
 * 当天: 15:36(显示时:分)  昨天: 昨天(显示昨天) 昨天以前: 01-18(显示月:日)
 * @param time 
 */
export default function handleTime(time: string) {
  let showTime: string;
  const [NOW_MM, NOW_DD] = moment().format('MM-DD').split('-');
  const [MSG_MM, MSG_DD] = moment(time).format('MM-DD').split('-');
  if (NOW_MM === MSG_MM) {
    if (NOW_DD === MSG_DD) {
      showTime = moment(time).format('HH:mm')
    } else if (Number(NOW_DD) === Number(MSG_DD) + 1) {
      showTime = '昨天'
    } else {
      showTime = moment(time).format('MM-DD')
    }
  } else {
    showTime = moment(time).format('MM-DD');
  }
  return showTime
}
