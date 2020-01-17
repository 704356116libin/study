import * as React from 'react'
import SimulationReviewForm from '../reviewFormLibrary/simulationReviewForm';
import classNames from 'classnames';
import './index.scss';
import ReviewFormLibrary from '../reviewFormLibrary';
interface CustomReviewFormProps {
  formInfo?: any,
  onChange?: any
}
/**
 * 自定义表单拖拽控件
 */
export default class CustomReviewForm extends React.Component<CustomReviewFormProps, any> {
  static getDerivedStateFromProps(nextProps: any, state: any) {
    // Should be a controlled component.
    if ('formInfo' in nextProps) {
      return {
        ...state,
        formDatas: nextProps.formInfo
      };
    }
    return null;
  }
  // 自定义form组件中,state中接受父组件传递的参数，之后用于动态处理数据
  state = {
    formLibraryVisible: false,
    formDatas: this.props.formInfo || []
  }
  editFormLibrary = (formDatas: any) => {
    this.setState({
      formLibraryVisible: true,
      formDatas
    })
  }
  sureFormData = (formData: any) => {
    this.setState({
      formLibraryVisible: false,
      formDatas: formData
    })
    // 接受父组件传递的onChange事件，把处理好的数据传递给父组件
    const onChange = this.props.onChange;
    if (onChange) {
      onChange(formData);
    }
  }
  hideFormLibrary = () => {
    this.setState({
      formLibraryVisible: false
    })
  }
  render() {
    const { formDatas, formLibraryVisible } = this.state;
    return (
      <div className="custom-review-form">
        <div style={{ border: '1px solid rgb(221, 221, 221)', borderBottom: "none" }}>
          <span
            className={classNames(['cursor-pointer', 'text'])}
            onClick={() => this.editFormLibrary(formDatas)}>
            编辑控件
          </span>
          <span>（您可以点击此处进行拖拽编辑控件）</span>
        </div>
        <div className={classNames(['beautiful-scroll-bar-hover', 'preview'])} >
          {formDatas && <SimulationReviewForm formData={formDatas} />}
        </div>
        <ReviewFormLibrary
          visible={formLibraryVisible}
          onOk={this.sureFormData}
          onCancel={this.hideFormLibrary}
          deleteData={formDatas}
        />
      </div>
    )
  }
}