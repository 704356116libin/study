import React, { FormEvent } from 'react';
import { Form, Button } from 'antd';
import FormItem from 'antd/lib/form/FormItem';
import { FormComponentProps } from 'antd/lib/form';
import { itemType } from './reviewFormItem'

export interface ReviewFormItem {
  field: {
    label: string;
    required: boolean;
  },
  type: string;

}

export interface ParseReviewProps extends FormComponentProps {
  formData?: ReviewFormItem[];
  /** 是否启用提交按钮 */
  enableSubmit?: boolean;
  /** 提交按钮文本 */
  submitText?: string;
  /** 提交后触发方法 */
  onSubmit?: (event: FormEvent<any>) => void;
  /** 提交按钮布局 */
  submitCol?: any;
  /** 布局方式: 紧凑 | 宽松 */
  layout?: "compact" | "loose" | { [key: string]: any };
  /** 容器样式 */
  style?: React.CSSProperties;
}


class ParseReview extends React.Component<ParseReviewProps> {

  static defaultProps: {
    enableSubmit: boolean;
    submitText: string;
    layout: "compact" | "loose";
  } = {
      enableSubmit: false,
      submitText: '提交',
      layout: "loose"
    }

  render() {
    const { layout, enableSubmit, submitText, onSubmit, submitCol, style } = this.props;
    const { getFieldDecorator } = this.props.form;

    const formItemLayout = typeof layout !== 'string' ?
      layout
      :
      {
        labelCol: {
          xs: { span: 24 },
          sm: { span: this.props.layout === 'loose' ? 6 : 3 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 10 },
        }
      };
    const formData: ReviewFormItem[] | undefined = this.props.formData;

    return (
      <Form
        style={{ marginTop: 30, ...style }}
        onSubmit={enableSubmit ? onSubmit : undefined}
      >
        {
          formData && formData.map(({ field: { label, required }, type }) => {
            const ItemWrapper = itemType();

            return (
              ItemWrapper[label]
                ? (
                  <FormItem
                    {...formItemLayout}
                    label={label}
                    key={ItemWrapper[label].formId}
                  >
                    {
                      getFieldDecorator(
                        ItemWrapper[label].formId as never,
                        ItemWrapper[label].buildOption(required),
                      )(
                        label === '完成时间' ? <ItemWrapper.完成时间.component /> : ItemWrapper[label].component
                      )
                    }
                  </FormItem>
                )
                : null
            )
          })
        }
        {
          formData && formData.length !== 0 && enableSubmit ? (
            <FormItem wrapperCol={submitCol}>
              <Button type="primary" htmlType="submit">{submitText}</Button>
            </FormItem>
          ) : null
        }

      </Form>
    )
  }
}
export default Form.create<ParseReviewProps>()(ParseReview)