// $(document).ready(function () {
//   var term = $("#tags").val();
//   $("#tags").autocomplete({
//     appendTo: ".datafetch",
//     minLength: 3,
//     source: function (requete, response) {
//       $.ajax({
//         type: "GET",
//         url: `/autocomplete/${term}`,
//         dataType: "json",
//         data: $("#tags").val(),

//         success: function (data) {
//           console.log(data);
//           response(
//             $.map(data, function (item) {
//               return {
//                 label: item,
//                 value: item,
//               };
//             })
//           );
//         },
//       });
//     },
//   });
// });
