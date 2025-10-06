<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            position: relative;
            background-color: #eee;
            font-size: 85%;
        }

        #print {
            position: fixed;
        }

        #content {
            width: 57mm;
            padding: 3mm;
            border: 1px #D3D3D3 solid;
            background: #fff;
            box-shadow: 0 0 5px rgb(0 0 0 / 10%);
			overflow-wrap: break-word;
        }
    </style>
    <script>
        function printPDF() {
			let height = document.getElementById('content').scrollHeight;
			var printWindow = window.open('', 'G7 Autocare', `height=${height},width=216`);
			printWindow.document.write('<!DOCTYPE html><html><head>');
			printWindow.document.write(`<title></title>`);
			printWindow.document.write(`<link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css" />`);
			printWindow.document.write(`
			<style>
				@page {
					size: 57mm ${Math.ceil(height / 3.7795275591)}mm portrait;
				}
				body {
					margin: 0px;
				}

				.MsoBodyTextIndent {
					margin-right: 0!important;
				}

				#content {
					width: 57mm;
					padding: 3mm;
					overflow-wrap: break-word;
				}
			</style>
			`);
			printWindow.document.write('</head><body >');
			printWindow.document.write(document.getElementById('content').outerHTML);
			printWindow.document.write('</body></html>');
			printWindow.document.close();
			printWindow.onload = function() {
				printWindow.print();
			}
		}
    </script>
</head>

<body>
    <button onclick="printPDF()" style="padding: 5px 10px" class="d-print-none" id="print">In</button>
    <div style="display: flex; justify-content: center">
        <div id="content">
            <div style="width: 100%">{!! $template !!}</div>
        </div>
    </div>
</body>

<script>
	printPDF();
</script>

</html>
