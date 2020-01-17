import React from 'react';
import { Input, Button, Table, Icon } from 'antd';
import Highlighter from 'react-highlight-words';


export default class SearchTable extends React.Component<any, any> {

  searchInput = React.createRef() as any;
  state = {
    searchText: '',
  };

  getColumnSearchProps = (dataIndex: any) => ({
    filterDropdown: ({
      setSelectedKeys, selectedKeys, confirm, clearFilters,
    }: any) => (
        <div style={{ padding: 8 }}>
          <Input
            ref={node => { this.searchInput = node; }}
            placeholder={`搜索 ${dataIndex}`}
            value={selectedKeys[0]}
            onChange={e => setSelectedKeys(e.target.value ? [e.target.value] : [])}
            onPressEnter={() => this.handleSearch(selectedKeys, confirm)}
            style={{ width: 188, marginBottom: 8, display: 'block' }}
          />
          <Button
            type="primary"
            onClick={() => this.handleSearch(selectedKeys, confirm)}
            icon="search"
            size="small"
            style={{ width: 90, marginRight: 8 }}
          >
            搜索
          </Button>
          <Button
            onClick={() => this.handleReset(clearFilters)}
            size="small"
            style={{ width: 90 }}
          >
            重置
          </Button>
        </div>
      ),
    filterIcon: (filtered: any) => <Icon type="search" style={{ color: filtered ? '#1890ff' : undefined }} />,
    onFilter: (value: any, record: any) => record[dataIndex].toString().toLowerCase().includes(value.toLowerCase()),
    onFilterDropdownVisibleChange: (visible: any) => {
      if (visible) {
        setTimeout(() => this.searchInput.select());
      }
    },
    render: (text: any) => (
      <Highlighter
        highlightStyle={{ backgroundColor: '#ffc069', padding: 0 }}
        searchWords={[this.state.searchText]}
        autoEscape
        textToHighlight={text.toString()}
      />
    ),
  })

  handleSearch = (selectedKeys: any, confirm: any) => {
    confirm();
    this.setState({ searchText: selectedKeys[0] });
  }

  handleReset = (clearFilters: any) => {
    clearFilters();
    this.setState({ searchText: '' });
  }

  render() {

    const { columns, dataSource, ...restProps } = this.props;

    return <Table {...restProps} columns={columns.map((item: any) => ({ ...item, ...this.getColumnSearchProps(item.dataIndex) }))} dataSource={dataSource} />;
  }
}