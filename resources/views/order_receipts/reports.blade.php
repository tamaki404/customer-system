

<div class="report-con">
        <a class="go-back-a" href="/purchase_order"><- Order</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: auto;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>
        
        <div class="report-header">
            <h3>Report</h3>
            <p>{{$report->reported_at}}</p>
        </div>

        <div class="report-subject">
            <p>Subject:</p>
            <p>{{$report->report_subject}} </p>
        </div>
        <div class="report-feedback">
            <p>Feedback</p>
            <p class="feedback">{{$report->feedback}}</p>
        </div>

</div>

<style>
    .report-con{
        display: flex;
        flex-direction: column;
        flex: 1;
        width: 90%;
        height: 90%;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        border-radius: 10px;
        justify-self: center;
        align-self: center;
        padding: 25px;
        gap: 10px;
    }
    .report-con .report-header{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }
    .report-con .report-subject{
        display: flex;
        flex-direction: row;
        gap: 5px;
        background-color: ;
        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        border-radius: 10px;
        padding: 15px;
        width: 200px;
        gap: 5px;

    }
    .report-con .report-feedback{
        display: flex;
        flex-direction: column;
        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        border-radius: 10px;
        padding: 15px;
        gap: 5px;
        width: 500px;
    }
    .report-feedback .feedback{
        width: 100%

    }
    .report-con div p{
        margin: 0;
        font-size: 14px;
        color: #333;
    }
</style>