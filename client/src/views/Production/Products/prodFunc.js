import { read, writeFileXLSX, } from "xlsx";
import XLSX from "xlsx"


export const exportTable = (type, datatable, cols) => {
    let records = datatable.value.getSelectedRows()
    let columns = datatable.value.getColumnFilters()
    if (!records?.length) {
        records = production.products
    }
    const filename = 'Products_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime();

    if (type === 'csv' || type === 'txt') {
        const coldelimiter = ','
        const linedelimiter = '\n'
        let result = cols.value
            .filter((d) => !d.hide)
            .map((d) => {
                return d.title
            })
            .join(coldelimiter)
        result += linedelimiter
        records.map((item) => {
            cols.value
                .filter((d) => !d.hide)
                .map((d, index) => {
                    if (index > 0) {
                        result += coldelimiter
                    }
                    const val = d.field.split('.').reduce((acc, part) => acc && acc[part], item)
                    result += val
                });
            result += linedelimiter
        });

        if (result === null) return;

        if (type === 'csv') {
            const plainData = records.map(item => {
                delete item['name']
                delete item.client_id
                columns.forEach(colum => {
                    if(colum.hide){
                        delete item[colum.field]
                    }
                    if(colum.title == "Client"){
                        item.client = item.client.name
                    }
                });
                if(isRef(item)) {
                    return toRaw(unref(item))
                }
                if(isReactive(item)) {
                    return toRaw(item)
                }
                return item
            })
            let ws = XLSX.utils.json_to_sheet(plainData);
            let wb = XLSX.utils.book_new() 
            console.log(ws)
            XLSX.utils.book_append_sheet(wb, ws, 'Products')

            const COL_WIDTH = 100;
            let COL_INDEXES = [0,1,2,3,4,5,6,7,8,9]
            if(!ws["!cols"]) ws["!cols"] = []

            COL_INDEXES.forEach(COL_INDEX => {
                if(!ws["!cols"][COL_INDEX]) ws["!cols"][COL_INDEX] = {wch: 8}
                ws["!cols"][COL_INDEX].wpx = COL_WIDTH;
            });

            if(!ws["!rows"]) ws["!rows"] = []

            if(!ws["!rows"][0]) ws["!rows"][0] = {hpx: 30};
            ws["!rows"][0].hpx = 30;

            ws['!autofilter'] = { ref:"A1:I1" };

            XLSX.writeFile(wb, 'products.xlsx', {cellStyles: true})
        }

        if (type === 'txt') {
            var data = 'data:application/txt;charset=utf-8,' + encodeURIComponent(result);
            var link = document.createElement('a')
            link.setAttribute('href', data);
            link.setAttribute('download', filename + '.txt')
            link.click();
        }
    } else if (type === 'print') {
        let rowhtml = '<p>' + filename + '</p>'
        rowhtml +=
            '<table style="width: 100%; " cellpadding="0" cellcpacing="0"><thead><tr style="color: #515365; background: #eff5ff; -webkit-print-color-adjust: exact; print-color-adjust: exact; "> ';
        cols.value
            .filter((d) => !d.hide)
            .map((d) => {
                rowhtml += '<th>' + d.title + '</th>'
            });
        rowhtml += '</tr></thead>'
        rowhtml += '<tbody>'

        records.map((item) => {
            rowhtml += '<tr>'
            cols.value
                .filter((d) => !d.hide)
                .map((d) => {
                    const val = d.field.split('.').reduce((acc, part) => acc && acc[part], item)
                    rowhtml += '<td>' + (val ? val : '') + '</td>'
                });
            rowhtml += '</tr>'
        });
        rowhtml +=
            '<style>body {font-family:Arial; color:#495057;}p{text-align:center;font-size:18px;font-weight:bold;margin:15px;}table{ border-collapse: collapse; border-spacing: 0; }th,td{font-size:12px;text-align:left;padding: 4px;}th{padding:8px 4px;}tr:nth-child(2n-1){background:#f7f7f7; }</style>';
        rowhtml += '</tbody></table>'
        const winPrint = window.open('', '', 'left=0,top=0,width=1000,height=600,toolbar=0,scrollbars=0,status=0')
        winPrint.document.write('<title>' + filename + '</title>' + rowhtml)
        winPrint.document.close()
        winPrint.focus()
        winPrint.onafterprint = () => {
            winPrint.close()
        };
        winPrint.print()
    } else if(type === "excel"){
         excelParser().exportDataFromJSON(rows.value, null, null);
    }
};
