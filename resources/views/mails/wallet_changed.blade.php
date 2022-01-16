<html>
<title>{{$subjectMatter}}</title>

</html>

<body>
    <h3>Hi {{$username}}</h3>
    <p>
        This is to inform you that your 54gene loan wallet has been {{$amount > 0 ? "credited" : "debited"}}
        <br>
        <strong>Amount: </strong> &#8358; {{number_format(abs($amount))}}
    </p>

</body>