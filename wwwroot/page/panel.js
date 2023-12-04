export default {
  template: await (await fetch('./page/panel.htm')).text(),
  data() {
    return {
      items: [],
      openKeys: ['main'],
      selectedKeys: ['index'],
    }
  },
  async created() {
    const children = [];
    children.push({ key: 'index', label: '面板首页' });
    children.push({ key: 'table', label: '测试表格' });
    children.push({ key: 'table2', label: '测试表格2' });
    const menu = { key: 'main', label: '数据查看', children: children };
    this.items.push(menu);
  },
  methods: {
    handleClick(e) {
      console.log(e.key);
      if (e.key === 'table') {
        this.$router.push('/panel/table');
      } else {
        this.$router.push('/panel');
      }

    }
  },
}
