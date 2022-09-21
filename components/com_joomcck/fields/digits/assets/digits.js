function number_format(number, decimals, point, separator)
{
	if(!isNaN(number))
	{
		point = point ? point : '.';
		number = number.toString().split('.');
		if(separator)
		{
			var tmp_number = new Array();
			for(var i = number[0].length, j = 0; i > 0; i -= 3)
			{
				var pos = i > 0 ? i - 3 : i;
				tmp_number[j++] = number[0].substring(i, pos);
			}
			number[0] = tmp_number.reverse().join(separator);
		}
		if(decimals)
		{
			number[1] = number[1] ? number[1] : '0';
			number[1] = Math.round(parseFloat(number[1].substr(0, decimals) + '.' + number[1].substr(decimals, number[1].length), 10));
			var size = decimals - number[1].toString().length;
			for(var i = 0; i < size; i++) number[1] += '0';
		}
		return(number.join(point));
	}
	else return(null);
}