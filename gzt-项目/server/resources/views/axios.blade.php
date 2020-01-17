<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passport测试</title>
    <style >
        p.error{
            color: #880000;
            display: none;
        }
        div#show_captcha{
            display: none;
        }
    </style>
</head>
<script src="js/app.js"></script>
<body>
<input id="get_clients" type="button" value="返回认证用户的所有客户端"><br/>
<input id="make_client" type="button" value="新建一个客户端"><br/>
<label>客户端id:</label><select id="clients_select" name="client_id"></select><br/>
<input id="update_client" type="button" value="更新客户端信息"><br/>
<input id="delete_client" type="button" value="删除指定客户端"><br/>
<input id="password_grant" type="button" value="通过密码授权"><br/>
<input id="get_scope" type="button" value="返回程序中作用域"><br/>
<input id="api_token" type="button" value="ApiToken测试"><br/>
<input id="refresh_token" type="button" value="刷新accecc_token"><br/>

<script type="text/javascript">
    $(document).ready(function () {
        $('#delete_client').click(function () {
            client_id=$('#clients_select').val();
            /**
             * 删除客户端只是revoke=1
             */
            axios.delete('/oauth/clients/'+client_id)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                });
        });
        $('#get_clients').click(function () {
            axios.get('/oauth/clients')
                .then(function(response){
                    response.data.forEach(function (v) {
                        $('#clients_select').append( $('<option  >'+v.id+'</option>'));
                    });
                   $data={
                       client_id:response.data[0].id,
                       redirect_uri:response.data[0].redirect,
                   }
                   console.log($data);
                   axios.get('/redirect',{
                           params:$data
                       })
                       .then(function (res) {
                           alert('请求成功');
                       })
                       .catch(function (e) {
                           alert('请求失败');
                       })
                })
                .catch(function (response) {
                    console.log(response);
                });
        });
        $('#make_client').click(function () {
            const data = {
                name: 'libin',
                password_client:1,
                redirect: 'http://gzt.test/ajax'
            };
            axios.post('/oauth/clients',data)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                })
            ;
        });
        $('#update_client').click(function () {
            const data = {
                name: 'libin6666',
                redirect: 'http://gzt.test/ajax',
            };
            client_id=$('#clients_select').val();
            axios.put('/oauth/clients/'+client_id,data)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                });
        });
        $('#password_grant').click(function () {//密码授权方式
            header={
                headers : {
                    Accept : 'application/json',
                    Authorization : 'Bearer '+'123456',
                }
            };
            client_id=$('#clients_select').val();
            axios.post('/pwdGrant',{},header)
                .then(function (response) {
                    alert(response.data.code);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                });
        });
        $('#get_scope').click(function () {
            // axios.get('/oauth/scopes')//拿到所有作用域
            //     .then(response => {
            //     console.log(response.data);
            //  });

            // axios.get('/oauth/personal-access-tokens')//获取个人得access_token
            //     .then(response => {
            //     console.log(response.data);
            // });
            const data = {
                name: 'Token N1ame',
                scopes: []
            };
            /*
             * /oauth/personal-access-tokens:用于创建新的个人访问令牌。
             *  /oauth/personal-access-tokens:用于创建新的个人访问令牌。
             */
            axios.get('/oauth/personal-access-tokens', data)
                .then(response => {
                console.log(response);
             })
            .catch (response => {
                    // 列出响应中错误...
            });
        });
        /**
         * api_token请求头携带验证
         */
        $('#api_token').click(function () {
            $token1='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM1YWMxYzdlZjgyYjAyMWE2ODM4ZjY4ZDZjNjhhNmE5OGE0ZGFjMDI1Njc1MTA0NGEwYWJjYzhkYjViNDJhYmNlZGYxZjYwZTgzYTljMWE5In0.eyJhdWQiOiIyIiwianRpIjoiYzVhYzFjN2VmODJiMDIxYTY4MzhmNjhkNmM2OGE2YTk4YTRkYWMwMjU2NzUxMDQ0YTBhYmNjOGRiNWI0MmFiY2VkZjFmNjBlODNhOWMxYTkiLCJpYXQiOjE1NDE3NTMyMzAsIm5iZiI6MTU0MTc1MzIzMCwiZXhwIjoxNTQzMDQ5MjMwLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.wRJDNe5mZsjjHV7OIVEoK3kHUDSXDbGPKQs84P0SssQc9o0FUGYYdBl0otRihJu_yMbcZBLgIJJpqcVo7n1RRc4THESOMkLZ2KMfsijQIQmqPYDQm1b7bHmCmiRKQeZJiCqtk8iO07KLblPnjVe0VI-3i4uZol_vX80jYxwdFKjdREEjFzHY_Qm0g4DHmFzsCzqLZFHJq4knMW4vZu9mK2AwBt9TiEKUOZAV-X4FWcfc3rl0YwYPhDm-brFXyPDU8JOBle45pklA3fJemxmRWUZQ9Op6coedg7yft_DJfhnJR8LmAUhIvS1qEEyaaIWxSV9iX6kXT4Q2BaaKBZvdALoznI65-OUjC5-OZZWt9lJFBTUA-nknfyJb8lsCuRG8KP-nc91FyQUSKJLlJDVvFy-WfC-kLW4X9ZuHiX_EQVVY3OxburkF9gi0KTufYxW1cdqEZeB3Ksc7-YNkBeM0gqgt5qZiLBWwPmeJWQzU8RcZz5vq8rcNqSDNgkGuYu0DG7PYwxGh9Gox5ASTURP7Rf-822wkJJ2FeYrWFZOuyIzcoHtwoCjpSf-N-f4F1OlPjJgqkYqwN9KGfaRyPxGNsDzLeztVwJCLAi5n2vCMiXW43da7vJFFWGFGXeMsUn_3VkRX3qySpguYdZYKSQyikKexTSbzKWtgO6ByU4JYXds';
            const data = {
                headers : {
                    Accept : 'application/json',
                    Authorization : 'Bearer '+$token1,
                }
            };
            console.log(data);
            client_id=$('#clients_select').val();
            axios.post('/api_test',{},data)
                .then(function (response) {
                    alert(response.data);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                });
        });
        $('#refresh_token').click(function () {
            $refresh_token='def5020063ed0c8338e688bc2aa06b5c92976cd512a1498ab231b4f4f6a391122627ccec1f23569ae23c7089fd9045aa77111cf6d329cb4ef266e1efb62698e4afc2d3246c9e354af0b33b7e7234f33b46ce166b26c3a9817dffc4ff76f477ede1c8d2042b308b05a36a56f692f50f4f2c3a55ecc1369948ec8691f89e43966e5c4d11fa4a044bd2c58ede147c27e254650f73b8d52d26b079c4b0a08c61a3c0f17ea041b9e83cf2143986239d2148ff41a2f4192415889d7daa8ce11c94521ea3bdb1a73237cf48bad07d56592084dd4bea0fc98f146dcc4aa8ec93cc00dcb391b125f3ef7a86e257f21d3678d25f5042cd84b3b5fdd04007c04674f4e05a66bf59c9aa5b6f83d8dcb25454bd6880cc92539a19ad8c4293607a98de67093710f830a9a5bb8bd84e0b210b7e0ea5aab4c6681512f1f62459405ea5ec015776df76f58d15d8804516e28364c91c3d4770371fc9b75025c7522007c4757202ab745a';
            const data = {
               refresh_token:$refresh_token,
            };
            client_id=$('#clients_select').val();
            axios.post('/refreshToken',data)
                .then(function (response) {
                    alert(response.data);
                })
                .catch(function (error) {
                    alert('出错了!!!!!');
                });
        });

    });
</script>
</body>
</html>