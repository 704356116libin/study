import React from 'react';
import TextLabel from "../../../components/textLabel";
import moment from "moment";

/** 展示各方评审人员相关信息 */
export default function ReviewPersonnels(props: any) {

  const { visible, review, btnStatusMap } = props;

  return visible
    ? (
      <div>
        <div style={{ marginTop: '24px', padding: '12px', border: '1px solid #ddd' }}>
          <div style={{ paddingBottom: '12px', fontWeight: 'bold' }}>内部人员：</div>
          <div style={{ marginBottom: '12px' }}><span className="under-mark">负责人</span></div>
          {
            (() => {
              if (review) {
                if (review.detail_info.inside_user.duty_user.length === undefined) {
                  const { is_my, name, state, need_data, type, btn_status: { btn_back, btn_receive, btn_refuse_receive } } = review.detail_info.inside_user.duty_user;
                  return (
                    <div style={{ paddingBottom: '6px', overflow: 'hidden' }}>
                      <span>{is_my ? '我' : name}（{state}）</span>
                      <span style={{ float: 'right' }}>
                        {btn_back && btnStatusMap('btn_back', { need_data, type }).btn_back}
                        {btn_receive && btnStatusMap('btn_receive', { need_data, type }).btn_receive}
                        {btn_refuse_receive && btnStatusMap('btn_refuse_receive', { need_data, type }).btn_refuse_receive}
                      </span>
                    </div>
                  )
                }
              }
              return null
            })()
          }
          <div style={{ marginBottom: '12px' }}><span className="under-mark">参与人</span></div>
          {
            review && review.detail_info.inside_user.inside_join_user.map(({ form_data, opinion, is_my, name, state, btn_status, need_data, type }: any, index: number) => {
              const btns = [];
              for (const key in btn_status) {
                btn_status[key] && btns.push(btnStatusMap(key + '-' + index, {
                  need_data,
                  type
                })[key]);
              }
              return (
                <div key={`${name}-${index}`} style={{ paddingBottom: '6px', overflow: 'hidden' }}>
                  <span>{is_my ? '我' : name}（{state}）</span>
                  {
                    opinion && !Array.isArray(opinion) && (
                      <>
                        <TextLabel text='总结' />
                        <span>{opinion}</span>
                      </>
                    )
                  }
                  <span style={{ float: 'right' }}>{btns}</span>
                  {
                    review.detail_info.inside_user.duty_user.is_my && state === '待审核' && form_data && form_data.map(({ type, field, value }: any, index: any) => {

                      if (!value) {
                        return null
                      }
                      if (type === 'DATEPICKER') {
                        value = moment(value).format('YYYY-MM-DD HH:mm');
                      } else if (type === 'ANNEX' && value.length !== 0) {
                        return (
                          <div key={index}>
                            <TextLabel text={field.label} />
                            {value && value.map(({ name, oss_path }: any, k: any) => {
                              return (
                                <div key={k}><a target="_blank" rel="noopener noreferrer" href={oss_path}>{name}</a></div>
                              )
                            })
                            }
                          </div>
                        )
                      }
                      return (
                        <div>
                          <TextLabel text={field.label} />
                          <span>{value}</span>
                        </div>
                      )
                    })
                  }
                </div>
              )
            })
          }
        </div>
        {
          review && review.detail_info.company_partner.length !== 0 ? (
            <div style={{ marginTop: '24px', padding: '12px', border: '1px solid #ddd' }}>
              <div style={{ paddingBottom: '12px', fontWeight: 'bold' }}>合作伙伴：</div>
              {review.detail_info.company_partner.map(({ name, state, btn_status, need_data, type }: any, index: number) => {
                const btns = [];
                for (const key in btn_status) {
                  btn_status[key] && btns.push(btnStatusMap(key + '-' + index, {
                    need_data,
                    type
                  })[key]);
                }
                return (
                  <div key={`${name}-${index}`} style={{ paddingBottom: '6px', overflow: 'hidden' }}>
                    <span>{name}（{state}）</span>
                    <span style={{ float: 'right' }}>{btns}</span>
                  </div>
                )
              })}
            </div>
          ) : null
        }
        {
          review && review.detail_info.outside_user.length !== 0 ? (
            <div style={{ marginTop: '24px', padding: '12px', border: '1px solid #ddd' }}>
              <div style={{ paddingBottom: '12px', fontWeight: 'bold' }}>外部联系人：</div>
              {review.detail_info.outside_user.map(({ name, state, btn_status, need_data, type }: any, index: number) => {
                const btns = [];
                for (const key in btn_status) {
                  btn_status[key] && btns.push(btnStatusMap(key + '-' + index, {
                    need_data,
                    type
                  })[key]);
                }
                return (
                  <div key={`${name}-${index}`} style={{ paddingBottom: '6px', overflow: 'hidden' }}>
                    <span>{name}（{state}）</span>
                    <span style={{ float: 'right' }}>{btns}</span>
                  </div>
                )
              })}
            </div>
          ) : null
        }
      </div>
    )
    : null
}
