@extends('layouts.app')

@section('content')
    <div id="app">
        <div>
            <div class="ui grid">
                <div class="three wide column">
                    <div class="ui left fixed vertical inverted menu">
                        <div class="item">
                            <div class="header">城市</div>
                            <div class="menu">
                                <a class="item" @click="setCurrent({base:'city', analyze:'salary'})">薪酬</a>
                                <a class="item" @click="setCurrent({base:'city', analyze:'count'})">数量</a>
                            </div>
                        </div>
                        <div class="item">
                            <div class="header">招聘分类</div>
                            <div class="menu">
                                <a class="item" @click="setCurrent({base:'jobType', analyze:'salary'})">薪酬</a>
                                <a class="item" @click="setCurrent({base:'jobType', analyze:'count'})">数量</a>
                            </div>
                        </div>
                        <div class="item">
                            <div class="header">技术分类</div>
                            <div class="menu">
                                <a class="item" @click="setCurrent({base:'tech', analyze:'salary'})">薪酬</a>
                                <a class="item" @click="setCurrent({base:'tech', analyze:'count'})">数量</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="thirteen wide column">
                    <div id="main"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="http://cdn.bootcss.com/vue/1.0.22/vue.min.js"></script>
    <script src="http://cdn.bootcss.com/vue-resource/0.7.0/vue-resource.min.js"></script>
    <script src="/js/echarts.min.js"></script>
    <script src="/js/chart.js"></script>
    <script type="text/javascript">
        $(function () {
            //style echarts container
            var container = $('#main');
            var containerWidth = container.css('width');
            var containerHeight = parseInt(containerWidth) * 2 / 3 + 'px';
            container.css('height', containerHeight);

            //init echarts
            var myChart = echarts.init(document.getElementById('main'));
            myChart.showLoading();

            var vue = new Vue({
                el: '#app',
                data: {
                    current: {
                        base: 'city',
                        analyze: 'salary'
                    },
                    option: {}
                },
                created: function () {
                    this.fetchData()
                },
                watch: {
                    current: 'fetchData',
                    option: 'updateEchart'
                },
                methods: {
                    setCurrent: function (data) {
                        this.$set('current', data)
                    },
                    fetchData: function () {
                        this.$http.get('{{route('api')}}', this.current).then(function (response) {
                            this.option = new OptionCreator().create(response.data, this.current)
                        })
                    },
                    updateEchart: function () {
                        console.log(this.option)
                        myChart.hideLoading();
                        myChart.setOption(this.option);
                    }
                }
            })
        })
    </script>
@endsection
