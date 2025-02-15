(function ($) {
    //    "use strict";


    /*  Data Table
    -------------*/

    $('#bootstrap-data-table').DataTable({
        lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "Tout"]],
		language: {
			processing:     "Traitement en cours...",
			search:         "Rechercher&nbsp;:",
			lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
			info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
			infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
			infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			infoPostFix:    "",
			loadingRecords: "Chargement en cours...",
			zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			emptyTable:     "Aucune donnée disponible dans le tableau",
			paginate: {
				first:      "Premier",
				previous:   "Pr&eacute;c&eacute;dent",
				next:       "Suivant",
				last:       "Dernier"
			},
			aria: {
				sortAscending:  ": activer pour trier la colonne par ordre croissant",
				sortDescending: ": activer pour trier la colonne par ordre décroissant"
			}
		}
    });

    $('#bootstrap-data-table-export').DataTable({
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tout"]],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
		language: {
			processing:     "Traitement en cours...",
			search:         "Rechercher&nbsp;:",
			lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
			info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
			infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
			infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			infoPostFix:    "",
			loadingRecords: "Chargement en cours...",
			zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			emptyTable:     "Aucune donnée disponible dans le tableau",
			paginate: {
				first:      "Premier",
				previous:   "Pr&eacute;c&eacute;dent",
				next:       "Suivant",
				last:       "Dernier"
			},
			aria: {
				sortAscending:  ": activer pour trier la colonne par ordre croissant",
				sortDescending: ": activer pour trier la colonne par ordre décroissant"
			}
		}
    });

	$('#row-select').DataTable( {
        initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select class="form-control"><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);

							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );

					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );

})(jQuery);
