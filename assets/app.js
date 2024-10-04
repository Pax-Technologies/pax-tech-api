import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import './styles/PT-theme.scss';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */


import $ from 'jquery';

global.$ = global.jQuery = $;
import 'datatables.net';

import 'datatables.net-bs5';

$(document).ready( function () {
    $('#invoices-table').DataTable();
} );

//Script for invoice table
// $(document).ready(function() {
//     var table = $('#invoices-table').DataTable();
//
//     // Create input search for every column
//     $('#invoices-table tfoot th').each( function () {
//         var title = $(this).text();
//         $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
//     } );
//
//     // Apply the search
//     table.columns().every( function () {
//         var that = this;
//
//         $( 'input', this.footer() ).on( 'keyup change clear', function () {
//             if ( that.search() !== this.value ) {
//                 that
//                     .search( this.value )
//                     .draw();
//             }
//         } );
//     } );
// });


