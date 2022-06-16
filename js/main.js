//version: 1.1
jQuery(function($) {
  $(document).ready(function(){
    setupFormWeekSelection();
    setupFormEvents();
    autoSelectRadio();
    setupMobileMenuEvents();
  }); // end document ready

  /**
   * Select an entire week on clicking on the calendar row
   */
  function setupFormWeekSelection() {
    $(document).on('click', '.ui-datepicker-week-end.appointable ~ .ui-datepicker-unselectable.not_appointable.appointable', function (e) {
      $(this).closest('tr').find('.ui-datepicker-week-end.appointable').click();
    });

    // Create an observer instance to add classes to the selected and next weeks
    var observer = new MutationObserver(function( mutations ) {
      mutations.forEach(function( mutation ) {	
          mutation.addedNodes.forEach(
            function(node) {
                if( $(node).hasClass("ui-datepicker-calendar")){	
                    $(node).find('.highlighted_day').parent().addClass('selected-week');
                    $(node).find('.highlighted_day').parent().next('tr').addClass('next-week');
                }
            }
        );	
      });  
    });
    // Configuration of the observer:
    var config = { 
        childList: true,
        attributes: true,
        subtree: true,
        characterData: true
    }; 
    var targetNode = $('.ui-datepicker-inline')[0];
    observer.observe(targetNode, config);  
  }
   /**
     * Function for adding days to a date
     */
    function addDays(date, days) {
      var result = new Date(date);
      result.setDate(result.getDate() + (days-1));
      return result;
  }
  function setupFormEvents() {

    var selectedDate = new Date();

    var form = $('.wc-appointments-appointment-form-wrap.cart');
    // get changed durations
    // form.on( 'addon-duration-changed', function(event, duration) {
    //     var currentForm = $(this);

    //     var addon_duration = parseInt( duration, 10 );
    //     var checkin_checkout_div = currentForm.find('.wc-appt-checkin-checkout-dates');

    //     var type = 'other';
    //     if(checkin_checkout_div.hasClass('type-reservation')) {
    //         addon_duration++;
    //         type = 'reservation';
    //     }
    //     var endDate = addDays(selectedDate, addon_duration);
    //     var month = endDate.toLocaleString('default', { month: 'long' });

    //     if(type == 'reservation') {
    //         $(currentForm).find('.checkout-value').html('11-12 PM ' + month + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
    //     } else {
    //         $(currentForm).find('.checkout-value').html(month + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
    //     }

    //     // see if there are unavailble dates that have been selected
    //     var selected_days = $(currentForm).find('.ui-datepicker-selected-day');
    //     $(selected_days).each(function() {
    //         if($(this).hasClass('fully_scheduled') || $(this).hasClass('not_appointable')) {
    //             $(currentForm).find('.wc-appt-error').slideDown();
    //             $(currentForm).find('.wc-appointments-appointment-form-button').prop('disabled', true);
    //         } else {
    //             $(currentForm).find('.wc-appt-error').slideUp();
    //             $(currentForm).find('.wc-appointments-appointment-form-button').prop('disabled', false);
    //         }
    //     });
    //     if(type == 'reservation') {
    //         $(currentForm).find('.ui-datepicker-checkout-day').removeClass('ui-datepicker-checkout-day');

    //         var last_selected_day = $(selected_days).last();

    //         if(last_selected_day.length) {
    //             $(currentForm).find('.ui-datepicker-checkout-day').removeClass('ui-datepicker-checkout-day');
    //             var add_checkout_day = last_selected_day.nextAll( 'td' ).add( last_selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
    //         } else {
    //             var selected_day = $(currentForm).find('.ui-datepicker-current-day');
    //             var add_checkout_day = selected_day.nextAll( 'td' ).add( selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
    //         }

    //     }
    // } );

    // get the start date
    form.on( 'date-selected', function(event, data) {
        var currentForm = $(this);
        var picker = currentForm.find('.picker');
        var stringDate = data;
        var date = stringDate.split("-"); 
        selectedDate = new Date(date[0],date[1]-1,date[2]);//Date object
        if(picker.data('duration_unit') == 'day') {
            // Get the reservation type
            var checkin_checkout_div = currentForm.find('.wc-appt-checkin-checkout-dates');

            // reset the added days when changing a date
            var type = 'reservation';
            // add one day to the reservation length
            var appointment_duration = picker.data( 'appointment_duration' ) ? parseInt(picker.data( 'appointment_duration' )) + 1 : 8;
            
            var month = selectedDate.toLocaleString('default', { month: 'long' });

            var endDate = addDays(selectedDate, appointment_duration);
            var endMonth = endDate.toLocaleString('default', { month: 'long' });
            $(currentForm).find('.checkin-value').html('3 - 4PM, ' + month + ' ' + date[2] + ', ' + date[0]);
            $(currentForm).find('.checkout-value').html('11 - 12 PM, ' + endMonth + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
           
        } // end day
   
      
    } );
    
    


  }

  /**
   * Auto select the first radio option if not already selected
   */
  function autoSelectRadio() {
    var form = $('.wc-appointments-appointment-form-wrap.cart');

    form.on( 'date-selected', function(event, data) {
      // see if there's radios to choose
      var add_ons = $('.wc-pao-addon-package');
      $(add_ons).each(function(i) {
        // get the first radio
        var radio = $(this).find('input[type="radio"]');
        if(radio.length) {
          // see if one is already checked 
          var already_checked = false;
          $(radio).each(function() {
            if($(this).prop('checked') == true) {
              already_checked = true
            }
          });

          if(!already_checked) {
            radio.first().prop('checked', 'true');
          }
        }
      });  
    });
  }

  /**
   * Handle the menu events to close the menu on mobile
   */
  function setupMobileMenuEvents() {
    $('a[href*=\\#]').on('click', function() {
      if (this.hash != '') {
          if (this.pathname === window.location.pathname) {
            $('#main-menu .menu-item-has-children').removeClass('et-touch-hover').removeClass('et-hover').removeClass('et-show-dropdown').addClass('hide-children');
          }
      }
  });
   
  }
});





