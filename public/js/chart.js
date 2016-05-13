function OptionCreator() {
    this.option = {
        title: {
            text: '',
            subtext: '数据来自拉勾'
        },
        tooltip: {
            trigger: 'item',
            axisPointer: {
                type: 'shadow'
            }
        },
        grid: {
            left: '10%',
            right: '10%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: true,
            nameGap: 30,
            splitArea: {
                show: false
            },
            splitLine: {
                show: false
            },
            data: []
        },
        yAxis: [
            {
                type: 'value',
                name: '',
                splitArea: {
                    show: true
                },
                boundaryGap: [0, 0.2]
            }
        ],
        series: []
    };
    this.create = function (data, params) {
        switch (params.analyze) {
            case 'salary':
                return this.analyzeSalary(data, params.base);
                break;
            case 'count':
                return this.analyzeCount(data, params.base);
                break;
        }
    };
    this.analyzeSalary = function (data, base) {
        var cities = data.map(function (value) {
            return value.key === '' ? '其他' : value.key
        });
        var seriesData = data.map(function (value) {
            var dataArray = [value.salary_min.value];
            for (var i in value.salary_stat.values) {
                dataArray.push(value.salary_stat.values[i])
            }
            dataArray.push(value.salary_max.value);
            return dataArray;
        });
        this.option.xAxis.data = cities;
        this.option.yAxis.name = '平均薪酬(月)';
        this.option.yAxis.axisLabel = {
            formatter: '{value}k'
        };
        this.option.series.push({
            name: '月薪',
            type: 'boxplot',
            tooltip: {
                formatter: function (param) {
                    return [
                        param.name + ': ',
                        'upper: ' + param.data[0],
                        'Q1: ' + param.data[1],
                        'median: ' + param.data[2],
                        'Q3: ' + param.data[3],
                        'lower: ' + param.data[4]
                    ].join('<br/>')
                }
            },
            data: seriesData
        });
        switch (base) {
            case 'city':
                this.option.title.text = '招聘数量最多的20个城市的薪酬分布';
                break;
            case 'jobType':
                this.option.title.text = '不同招聘分类的薪酬分布';
                break;
            case 'tech':
                this.option.title.text = '不同技术分类的薪酬分布';
                break;
        }
        return this.option;
    }
    this.analyzeCount = function (data, base) {
        var cities = data.map(function (value) {
            return value.key === '' ? '其他' : value.key
        });
        var seriesData = data.map(function (value) {
            return value.doc_count
        });
        this.option.xAxis.data = cities;
        this.option.yAxis.name = '数量';
        this.option.yAxis.axisLabel = {
            formatter: '{value}个'
        };
        this.option.series.push({
            name: '数量',
            type: 'bar',
            data: seriesData
        });
        switch (base) {
            case 'city':
                this.option.title.text = '招聘数量最多的20个城市';
                break;
            case 'jobType':
                this.option.title.text = '不同分类的招聘数量分布';
                break;
            case 'tech':
                this.option.title.text = '不同技术分类的招聘数量分布';
                break;
        }
        return this.option;
    }
}
