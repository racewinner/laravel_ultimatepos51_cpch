(function($){
    // Default options
    var defaults = {
      hourStart: 0,
      hourEnd: 23,
      minuteStep: 15,
      defaultTime: '',      // "HH:mm" or "HH:mm AM/PM" depending on hour12
      onChange: null
    };
  
    // Helper: format hour per 12h or 24h
    function formatHour(h){
      return ('0' + h).slice(-2);
    }
  
    // Plugin definition
    $.fn.timepicker = function(options){
      var settings = $.extend({}, defaults, options);

      // Build time picker UI for each matched element
      return this.each(function(){
        var $container = $(this);
        $container.addClass('tp-container');
        $container.attr('aria-label', 'Time picker');

        // default time
        const defaultTime = $container.data('default-time');
        if(defaultTime) settings.defaultTime = defaultTime;
  
        // Create selects
        var $hour = $('<select class="tp-hour form-control" aria-label="Hour"></select>');
        var $minute = $('<select class="tp-minute form-control" aria-label="Minute"></select>');
  
        // If 12h mode, add an AM/PM select
        var $ampm = $('<select class="tp-ampm form-control" aria-label="AM or PM"></select>');

        // Populate hours
        function populateHours(){
          $hour.empty();
          var start = settings.hourStart;
          var end = settings.hourEnd;
          for (var h = start; h <= end; h++) {
            var display = formatHour(h);
            var label = display;
            var opt = $('<option></option>').val(h).text(label.toString().padStart(2, '0'));
            $hour.append(opt);
          }
        }
  
        // Populate minutes
        function populateMinutes(){
          $minute.empty();
          for (var m = 0; m < 60; m += settings.minuteStep){
            var txt = ('0' + m).slice(-2);
            $minute.append($('<option></option>').val(m).text(txt));
          }
        }
  
        // AM/PM options
        function populateAmPm(){
          if (!$ampm) return;
          $ampm.empty();
          $ampm.append($('<option value="AM">AM</option>'));
          $ampm.append($('<option value="PM">PM</option>'));
        }
  
        populateHours();
        populateMinutes();
        if ($ampm) populateAmPm();
  
        // Assemble UI
        var $wrapper = $('<div class="tp-wrapper" style="display:flex; gap:6px; align-items:center;"></div>');
        $wrapper.append($hour).append($minute);
        if ($ampm) $wrapper.append($ampm);
        $container.append($wrapper);
  
        // State
        var state = {
          h: 0,
          m: 0,
          ampm: 'AM'
        };
  
        // Helper: parse defaultTime
        function parseTime(str){
            if (!str) return null;
            str = str.trim();

            var parts = str.split(/[:\s]+/);
            if (parts.length >= 2) {
                var hh = parseInt(parts[0], 10);
                var mm = parseInt(parts[1], 10);
                var ap = (parts[2] || 'AM').toUpperCase();
                if (!isNaN(hh) && !isNaN(mm)) {
                return { h: hh, m: mm, ampm: ap };
                }
            }

            return null;
        }
  
        // Initialize with defaultTime if provided
        if (settings.defaultTime) {
          var dt = parseTime(settings.defaultTime);
          if (dt) {
            state.h = dt.h;
            state.m = dt.m;
            state.ampm = dt.ampm;
          }
        }
        // Sync selects to state
        function syncUI(){
          // Set hour select (value is 0..23)
          $hour.val(state.h);
          // Set minute select
          // Clamp to available minute steps
          var mOpt = $minute.find('option');
          var found = $minute.find('option[value="' + state.m + '"]');
          if (found.length) {
            $minute.val(state.m);
          } else {
            // fallback to closest step
            var step = settings.minuteStep;
            var mm = Math.round(state.m / step) * step;
            mm = Math.max(0, Math.min(59, mm));
            state.m = mm;
            $minute.val(state.m);
          }
          if ($ampm) {
            $ampm.val(state.ampm);
          }
        }
        syncUI();
        triggerChange();
  
        // Helper: build value and fire onChange
        function buildValue(){
          var h = parseInt($hour.val(), 10);
          var m = parseInt($minute.val(), 10);
          var ampm = $ampm ? $ampm.val() : null;

          return {
            h,
            m,
            ampm
          };
        }
  
        function triggerChange(){
          var v = buildValue();

          // Update internal state to reflect UI
          state.h = v.h;
          state.m = v.m;
          state.ampm = v.ampm;

          // Fire callback if provided
          if (typeof settings.onChange === 'function') {
            const s = formatValueForDisplay(v.h, v.m, v.ampm);
            $container.find('input.selected-time').val(s);

            settings.onChange.call($container[0], {
              h: v.h,
              m: v.m,
              ampm: v.ampm,
              value: s
            });
          }
        }
  
        // Helper to format string similar to input display
        function formatValueForDisplay(h, m, ampm){
            var label = ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2);
            if (ampm) label += ' ' + ampm;
            return label;
        }
  
        // Event handlers
        $hour.on('change', function(){
          state.h = parseInt($(this).val(), 10);
          syncUI();
          triggerChange();
        });
  
        $minute.on('change', function(){
          state.m = parseInt($(this).val(), 10);
          syncUI();
          triggerChange();
        });
  
        if ($ampm) {
          $ampm.on('change', function(){
            // Just re-calculate display; convert later on triggerChange
            var ap = $(this).val();
            state.ampm = ap;
            // No direct state change here; value is derived in triggerChange
            triggerChange();
          });
        }
      });
    };
  })(jQuery);