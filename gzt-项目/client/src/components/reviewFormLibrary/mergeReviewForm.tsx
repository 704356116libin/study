import React, { useState, useEffect } from 'react';
import { Form } from 'antd';
import { itemType } from './reviewFormItem';
import { WrappedFormUtils } from 'antd/lib/form/Form';
import update from 'immutability-helper';
import './mergeForm.scss';

const FormItem = Form.Item;

/**
 * 不带form的
 */
export default function MergeReviewForm({
  someBaseData,
  form,
  formData,
  onChange,
  layout,
  onClose,
  ...rest
}: {
  form?: WrappedFormUtils
  formData: any[];
  [propName: string]: any;
}) {

  const { getFieldDecorator } = form || {} as WrappedFormUtils;
  const [allFormData, setAllFormData] = useState(formData);

  useEffect(() => { // 更新formData
    setAllFormData(formData)
  }, [formData]);

  const formItemLayout = layout ? layout : {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 6 },
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 17 },
    }
  };
  /** 有一个表单触发 onChange 更新全部数据 */
  function updateOverallData(e: any, k: any, type: string) {

    let value;
    if (typeof e === 'string') {
      value = e
    } else if (type === 'DATEPICKER' || type === 'DATERANGE') {// 日期和附件等较特殊的控件 特殊处理
      value = e;
    } else if (type === 'ANNEX') {// 附件
      value = e.fileList
    } else {
      value = e.target.value
    }

    const nextFormData = update(allFormData, {
      [k]: {
        $merge: {
          value
        }
      }
    })
    // 把值传递出去
    onChange && onChange(nextFormData);
    // 更新状态
    setAllFormData(nextFormData)
  }

  return (
    <div className="merge-form-wrapper" {...rest}>
      {
        formData && formData.map(({ field: { label, required }, type, value }, k: number) => {

          const ItemWrapper = itemType(someBaseData, (e) => updateOverallData(e, k, type));

          return (
            ItemWrapper[label]
              ? (
                <div className="merge-form-item">
                  <FormItem
                    {...formItemLayout}
                    {
                    ...(label === '完成时间' ?
                      {
                        wrapperCol: {
                          xs: { span: 24 },
                          sm: { span: 18 },
                        }
                      }
                      :
                      null
                    )
                    }
                    key={ItemWrapper[label].formId}
                    label={label}
                    required={required}
                  >
                    {
                      // 存在的话校验，不存在的话不校验
                      getFieldDecorator
                        ?
                        getFieldDecorator(
                          ItemWrapper[label].formId as never,
                          ItemWrapper[label].buildOption(required, value),
                        )(
                          label === '完成时间' ? <ItemWrapper.完成时间.component /> : ItemWrapper[label].component
                        )
                        : label === '完成时间' ? <ItemWrapper.完成时间.component /> : ItemWrapper[label].component
                    }
                  </FormItem>
                  <i className="merge-form-close" onClick={() => onClose && onClose(k)}>x</i>
                </div>
              )
              : null
          )
        })
      }
    </div>
  )
}
