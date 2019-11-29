<template>
    <div class="topupintegral">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_remind_main">
                    <dl class="WSY_remind_dl02">
                        <p class="desp">{{name}}，剩余积分{{num}}</p></dl>
                    <dl class="WSY_remind_dl02">
                        <dt>充值积分：</dt>
                        <dd>
                            <input type="text" value="" maxlength="10" v-model="topupintegral"></dd>
                    </dl>
                    <div class="submit_div">
                        <Bigbutton btnval="保存" @click.native.prevent="commit"></Bigbutton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'


    export default {
        name: 'topupintegral',
        data: function() {
            return {
                name: '张三',
                num: 1000,
                topupintegral: ''
            }
        },
        components: {
            Bigbutton
        },
        methods: {
            commit() {

            }
        }
    }
</script>

<style scoped>
.WSY_remind_dl02{
	margin: 26px 0;
	text-align: left;
}
.WSY_remind_dl02 dt {
    line-height: 24px;
    width: 200px;
    font-weight: normal;
    font-size: 14px;
    text-align: right;
    display: inline-block;
    vertical-align: top;
}
.WSY_remind_dl02 dd {
    line-height: 24px;
    font-weight: normal;
    font-size: 14px;
    display: inline-block;
    margin-left: 10px;
    vertical-align: top;
}
.WSY_remind_dl02 input[type="text"] {
    width: 200px;
    height: 24px;
    border: 1px solid #dddddd;
    border-radius: 2px;
    box-sizing: border-box;
    padding: 0 0;
    padding-left: 5px;
}
.WSY_remind_dl02 dd .WSY_dd_div{
	margin-bottom: 15px;
}
.WSY_remind_dl02 dd .WSY_dd_div .WSY_dd_div_left{
	width: 150px;
	display: inline-block;
	vertical-align: middle;
}
.WSY_remind_dl02 dd .WSY_dd_div .WSY_dd_div_left input[type="radio"],
.WSY_remind_dl02 dd .WSY_dd_div .WSY_dd_div_left span{
	display: inline-block;
	vertical-align: middle;
	margin-top: 0;
}
.WSY_remind_dl02 dd .margind{
	margin: 0 10px;
}
.WSY_remind_dl02 dd .WSY_small_ipt{
	width: 60px;
}
.WSY_remind_dl02 dd .WSY_des_span{
	margin-left: 30px;
	display: inline-block;
	vertical-align: middle;
}
.submit_div {
    text-align: center;
    margin-bottom: 20px;
    overflow: hidden;
}

.WSY_remind_dl02 dt,.WSY_remind_dl02 .desp{
    margin-left: 50px;
    text-align: left;
    font-size: 14px;
}
.WSY_remind_dl02 dt{
    width: auto;
}
</style>