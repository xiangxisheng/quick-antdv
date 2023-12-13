const { reactive } = Vue;
const { useRouter, useRoute } = VueRouter;
const { Form, FormItem, Input, InputPassword, Checkbox, Space, Button } = antd;
export default async () => ({
  template: await (await fetch('./page/sign.htm')).text(),
  components: {
    AForm: Form,
    AFormItem: FormItem,
    AInput: Input,
    AInputPassword: InputPassword,
    ACheckbox: Checkbox,
    ASpace: Space,
    AButton: Button,
  },
  setup() {

    const formState = reactive({
      username: '',
      password: '',
      remember: false,
    });
    const onFinish = (values) => {
      console.log('Success:', values);
    };

    const onFinishFailed = (errorInfo) => {
      console.log('Failed:', errorInfo);
    };

    const router = useRouter();
    const route = useRoute();
    const title = route.name === '/sign-up' ? 'Register' : 'Login';

    return {
      router,
      route,
      title,
      formState,
      onFinish,
      onFinishFailed,
    };
  },
})