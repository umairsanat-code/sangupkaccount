   // function getAreaChart(elementId, data, labels, hero) {
   //
   //     var barchart = $('#' + elementId).get(0).getContext('2d')
   //     var myChart = new Chart(barchart, {
   //         type: 'bar',
   //         data: {
   //             labels: labels,
   //             datasets: [{
   //                 label: 'Monthly Message',
   //                 data: data,
   //                 borderWidth: 1,
   //                 fillColor: "#6cb2eb",
   //                 backgroundColor: "#6cb2eb",
   //             }]
   //         },
   //         options: {
   //             elements: {
   //                 rectangle: {
   //                     borderWidth: 2,
   //                     borderColor: '# 6 cb2eb ',
   //                     borderSkipped: 'bottom'
   //                 }
   //             },
   //             responsive: true,
   //             scales: {
   //                 yAxes: [{
   //                     ticks: {
   //                         beginAtZero: true
   //                     }
   //                 }]
   //             }
   //         }
   //     });
   // }
   function getAreaChart(elementId, data, labels, label) {
       var ctx = $('#' + elementId).get(0).getContext('2d');
       var myChart = new Chart(ctx, {
           type: 'bar',
           data: {
               labels: labels,
               datasets: [{
                   label: label,
                   data: data,
                   borderWidth: 1,
                   backgroundColor: "#6cb2eb",
                   borderColor: "#6cb2eb",
                   fill: false
               }]
           },
           options: {
               responsive: true,
               scales: {
                   yAxes: [{
                       ticks: {
                           beginAtZero: true
                       }
                   }]
               }
           }
       });
   }
