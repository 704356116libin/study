import React from 'react';
import DepartmentTreeModal from './departmentModal/DepartmentTreeModal';

export interface HigherDepartmentProps {
  // 首次默认选中的公司
  currentDefaultInfo: any;
  dataSource: any
  onChange?: any
}

export default class higherDepartment extends React.Component<HigherDepartmentProps, any> {

  componentDidMount() {

    this.setState({
      currentChooseDep: this.props.currentDefaultInfo
    })
    this.props.onChange(this.props.currentDefaultInfo.node_id)
  }
  componentDidUpdate(prevProps: any) {
    if (this.props.currentDefaultInfo !== prevProps.currentDefaultInfo) { // 判断是否变化
      this.setState({
        currentChooseDep: this.props.currentDefaultInfo
      })
      this.props.onChange(this.props.currentDefaultInfo.node_id)
    }
  }
  state = {
    depVisible: false,
    modalVisible: false,
    currentChooseDep: {} as any
  }
  showDepTree = () => {
    this.setState({
      depVisible: true,
      modalVisible: true
    })
  }
  closeModal = () => {
    this.setState({
      modalVisible: false
    })
  }
  handleOk = (params: any) => {

    const { departmentName, selectedKeys } = params;
    const ss = {
      'name': departmentName,
      'id': selectedKeys[0]
    }

    this.setState({
      currentChooseDep: ss,
      modalVisible: false
    })
    this.props.onChange && this.props.onChange(selectedKeys[0])
  }
  render() {

    const { showDepTree, closeModal, handleOk } = this;
    const { dataSource } = this.props;
    const { depVisible, modalVisible, currentChooseDep } = this.state;

    return (
      <>
        <div style={{ paddingLeft: '3px', marginTop: '5px', borderRadius: '3px', lineHeight: '30px', height: '33px', border: '1px solid #eee', cursor: 'pointer' }}
          onClick={showDepTree} >
          {currentChooseDep.name}
        </div>
        {
          dataSource && <DepartmentTreeModal
            dataSource={dataSource}
            depVisible={depVisible}
            visible={modalVisible}
            onCancel={closeModal}
            onOk={handleOk}
            currentDataInfo={currentChooseDep}
          />
        }
      </>
    )
  }
}