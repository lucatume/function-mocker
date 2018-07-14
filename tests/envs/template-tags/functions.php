<?php

if (!function_exists('tribe_is_day')) {
    /**
     * Single Day Test
     *
     * Returns true if the query is set for single day, false otherwise
     *
     * @category Events
     * @return bool
     */
    function tribe_is_day()
    {
        $tribe_ecp = Tribe__Events__Main::instance();
        $is_day = $tribe_ecp->displaying == 'day' ? true : false;
        return apply_filters('tribe_is_day', $is_day);
    }
}

if (!function_exists('tribe_get_day_link')) {
    /**
     * Link Event Day
     *
     * @category Events
     * @param string $date
     *
     * @return string URL
     */
    function tribe_get_day_link($date = null)
    {
        $tribe_ecp = Tribe__Events__Main::instance();
        return apply_filters('tribe_get_day_link', $tribe_ecp->getLink('day', $date), $date);
    }
}

if (!function_exists('tribe_get_linked_day')) {
    /**
     * Day View Link
     *
     * Get a link to day view
     *
     * @category Events
     * @param string $date
     * @param string $day
     *
     * @return string HTML linked date
     */
    function tribe_get_linked_day($date, $day)
    {
        $return = '';
        $return .= "<a href='" . esc_url(tribe_get_day_link($date)) . "'>";
        $return .= $day;
        $return .= '</a>';
        return apply_filters('tribe_get_linked_day', $return);
    }
}

if (!function_exists('tribe_the_day_link')) {
    /**
     * Output an html link to a day
     *
     * @category Events
     * @param string $date 'previous day', 'next day', 'yesterday', 'tomorrow', or any date string that strtotime() can parse
     * @param string $text text for the link
     *
     **/
    function tribe_the_day_link($date = null, $text = null)
    {
        $html = '';
        try {
            if (is_null($text)) {
                $text = tribe_get_the_day_link_label($date);
            }
            $date = tribe_get_the_day_link_date($date);
            $link = tribe_get_day_link($date);
            $earliest = tribe_events_earliest_date(Tribe__Date_Utils::DBDATEFORMAT);
            $latest = tribe_events_latest_date(Tribe__Date_Utils::DBDATEFORMAT);
            if ($date >= $earliest && $date <= $latest) {
                $html = '<a href="' . esc_url($link) . '" data-day="' . $date . '" rel="prev">' . $text . '</a>';
            }
        } catch (OverflowException $e) {
        }
        echo apply_filters('tribe_the_day_link', $html);
    }
}

if (!function_exists('tribe_get_the_day_link_label')) {
    /**
     * Get the label for the day navigation link
     *
     * @category Events
     * @param string $date_description
     *
     * @return string
     */
    function tribe_get_the_day_link_label($date_description)
    {
        switch (strtolower($date_description)) {
            case null:
                return esc_html__('Today', 'the-events-calendar');
            case 'previous day':
                return '<span>&laquo;</span> ' . esc_html__('Previous Day', 'the-events-calendar');
            case 'next day':
                return esc_html__('Next Day', 'the-events-calendar') . ' <span>&raquo;</span>';
            case 'yesterday':
                return esc_html__('Yesterday', 'the-events-calendar');
            case 'tomorrow':
                return esc_html__('Tomorrow', 'the-events-calendar');
            default:
                return date_i18n('Y-m-d', strtotime($date_description));
        }
    }
}

if (!function_exists('tribe_get_the_day_link_date')) {
    /**
     * Get the date for the day navigation link.
     *
     * @category Events
     * @param string $date_description
     *
     * @return string
     * @throws OverflowException
     */
    function tribe_get_the_day_link_date($date_description)
    {
        if (is_null($date_description)) {
            return Tribe__Events__Pro__Main::instance()->todaySlug;
        }
        if ($date_description == 'previous day') {
            return tribe_get_previous_day_date(get_query_var('start_date'));
        }
        if ($date_description == 'next day') {
            return tribe_get_next_day_date(get_query_var('start_date'));
        }
        return date('Y-m-d', strtotime($date_description));
    }
}

if (!function_exists('tribe_get_next_day_date')) {
    /**
     * Get the next day's date
     *
     * @category Events
     * @param string $start_date
     *
     * @return string
     * @throws OverflowException
     */
    function tribe_get_next_day_date($start_date)
    {
        if (PHP_INT_SIZE <= 4) {
            if (date('Y-m-d', strtotime($start_date)) > '2037-12-30') {
                throw new OverflowException(esc_html__('Date out of range.', 'the-events-calendar'));
            }
        }
        $date = date('Y-m-d', strtotime($start_date . ' +1 day'));
        return $date;
    }
}

if (!function_exists('tribe_get_previous_day_date')) {
    /**
     * Get the previous day's date
     *
     * @category Events
     * @param string $start_date
     *
     * @return string
     * @throws OverflowException
     */
    function tribe_get_previous_day_date($start_date)
    {
        if (PHP_INT_SIZE <= 4) {
            if (date('Y-m-d', strtotime($start_date)) < '1902-01-02') {
                throw new OverflowException(esc_html__('Date out of range.', 'the-events-calendar'));
            }
        }
        $date = date('Y-m-d', strtotime($start_date . ' -1 day'));
        return $date;
    }
}

if (!function_exists('tribe_get_display_end_date')) {
    /**
     * End Date formatted for display
     *
     * Returns the event end date that observes the end of day cutoff
     *
     * @category Events
     * @see      http://php.net/manual/en/function.date.php
     *
     * @param int|WP_Post $event        The event (optional).
     * @param bool        $display_time If true shows date and time, if false only shows date.
     * @param string      $date_format  Allows date and time formatting using standard php syntax.
     * @param string      $timezone     Timezone in which to present the date/time (or default behaviour if not set).
     *
     * @return string|null Date
     */
    function tribe_get_display_end_date($event = null, $display_time = true, $date_format = '', $timezone = null)
    {
        $timestamp = tribe_get_end_date($event, true, 'U', $timezone);
        $beginning_of_day = tribe_beginning_of_day(date(Tribe__Date_Utils::DBDATETIMEFORMAT, $timestamp));
        if (tribe_event_is_multiday($event) && $timestamp < strtotime($beginning_of_day)) {
            $timestamp -= DAY_IN_SECONDS;
        }
        $formatted_date = tribe_format_date($timestamp, $display_time, $date_format);
        /**
         * Filters the displayed end date of an event, which factors in the EOD cutoff.
         *
         * @since 4.5.10
         *
         * @see tribe_get_display_end_date()
         *
         * @param string      $formatted_date Formatted date for the last day of the event.
         * @param int         $timestamp      Timestamp calculated for the last day of the event.
         * @param mixed       $event          The event.
         * @param bool        $display_time   If true shows date and time, if false only shows date.
         * @param string      $date_format    Allows date and time formatting using standard php syntax.
         * @param string|null $timezone       Timezone in which to present the date/time (or default behaviour if not set).
         *
         */
        return apply_filters('tribe_get_display_end_date', $formatted_date, $timestamp, $event, $display_time, $date_format, $timezone);
    }
}

if (!function_exists('tribe_event_is_on_date')) {
    /**
     * Given a date and an event, returns true or false if the event is happening on that date
     * This function properly adjusts for the EOD cutoff and multi-day events
     *
     * @param null $date
     * @param null $event
     *
     * @return mixed|void
     */
    function tribe_event_is_on_date($date = null, $event = null)
    {
        if (null === $date) {
            $date = current_time('mysql');
        }
        if (null === $event) {
            global $post;
            $event = $post;
            if (empty($event)) {
                _doing_it_wrong(__FUNCTION__, esc_html__('The function needs to be passed an $event or used in the loop.', 'the-events-calendar'), '3.10');
                return false;
            }
        }
        $start_of_day = tribe_beginning_of_day($date, 'U');
        $end_of_day = tribe_end_of_day($date, 'U');
        $event_start = tribe_get_start_date($event, null, 'U');
        $event_end = tribe_get_end_date($event, null, 'U');
        // kludge
        if (!empty($event->_end_date_fixed)) {
            // @todo remove this once we can have all day events without a start / end time
            $event_end = date_create(date(Tribe__Date_Utils::DBDATETIMEFORMAT, $event_end));
            $event_end->modify('+1 day');
            $event_end = $event_end->format('U');
        }
        /* note:
         * events that start exactly on the EOD cutoff will count on the following day
         * events that end exactly on the EOD cutoff will count on the previous day
         */
        $event_is_on_date = Tribe__Date_Utils::range_coincides($start_of_day, $end_of_day, $event_start, $event_end);
        return apply_filters('tribe_event_is_on_date', $event_is_on_date, $date, $event);
    }
}

if (!function_exists('tribe_events_timezone_choice')) {
    /**
     * Event-specific wrapper for wp_timezone_choice().
     *
     * @since 4.6.5
     *
     * @param string $selected_zone
     * @param string $locale (optional)
     *
     * @return string
     */
    function tribe_events_timezone_choice($selected_zone, $locale = null)
    {
        /**
         * Opportunity to modify the timezone <option>s used within the timezone picker.
         *
         * @since 4.6.5
         *
         * @param string $html
         * @param string $selected_zone
         * @param string $locale
         */
        return apply_filters('tribe_events_timezone_choice', wp_timezone_choice($selected_zone, $locale), $selected_zone, $locale);
    }
}

if (!function_exists('tribe_display_current_events_slug')) {
    /**
     * display the events slug description
     *
     * @return string, the string to display
     */
    function tribe_display_current_events_slug()
    {
        echo '<p class="tribe-field-indent tribe-field-description description">' . esc_html__('The slug used for building the events URL.', 'the-events-calendar') . sprintf(esc_html__('Your current Events URL is %s', 'the-events-calendar'), '<code><a href="' . esc_url(tribe_get_events_link()) . '">' . tribe_get_events_link() . '</a></code>') . '</p>';
    }
}

if (!function_exists('tribe_display_current_single_event_slug')) {
    /**
     * display the event single slug description
     *
     * @return string, the string to display
     */
    function tribe_display_current_single_event_slug()
    {
        echo '<p class="tribe-field-indent tribe-field-description description">' . sprintf(esc_html__('You %1$scannot%2$s use the same slug as above. The above should ideally be plural, and this singular.%3$sYour single Event URL is like: %4$s', 'the-events-calendar'), '<strong>', '</strong>', '<br>', '<code>' . trailingslashit(home_url()) . tribe_get_option('singleEventSlug', 'event') . '/single-post-name/</code>') . '</p>';
    }
}

if (!function_exists('tribe_display_current_ical_link')) {
    /**
     * display the iCal description
     *
     * @return string, the string to display
     */
    function tribe_display_current_ical_link()
    {
        if (function_exists('tribe_get_ical_link')) {
            echo '<p id="ical-link" class="tribe-field-indent tribe-field-description description">' . esc_html__('Here is the iCal feed URL for your events:', 'the-events-calendar') . ' <code>' . tribe_get_ical_link() . '</code></p>';
        }
    }
}

if (!function_exists('tribe_is_community_my_events_page')) {
    /**
     * Tests if the current page is the My Events page
     *
     * @return bool whether it is the My Events page.
     * @since 1.0.1
     */
    function tribe_is_community_my_events_page()
    {
        if (!class_exists('Tribe__Events__Community__Main')) {
            return false;
        }
        return Tribe__Events__Community__Main::instance()->isMyEvents;
    }
}

if (!function_exists('tribe_is_community_edit_event_page')) {
    /**
     * Tests if the current page is the Edit Event page
     *
     * @return bool whether it is the Edit Event page.
     * @author Paul Hughes
     * @since 1.0.1
     */
    function tribe_is_community_edit_event_page()
    {
        if (!class_exists('Tribe__Events__Community__Main')) {
            return false;
        }
        return Tribe__Events__Community__Main::instance()->isEditPage;
    }
}

if (!function_exists('tribe_get_single_ical_link')) {
    /**
     * iCal Link (Single)
     *
     * Returns an ical feed for a single event. Must be used in the loop.
     *
     * @return string URL for ical for single event.
     */
    function tribe_get_single_ical_link()
    {
        $output = tribe('tec.iCal')->get_ical_link('single');
        /**
         * Filters the "Export Event" iCal link on single events.
         *
         * @param string $output The URL for the "Export Event" iCal link on single events.
         */
        return apply_filters('tribe_get_single_ical_link', $output);
    }
}

if (!function_exists('tribe_get_ical_link')) {
    /**
     * iCal Link
     *
     * Returns a sitewide "Export Events" iCal link
     *
     * @return string URL for ical dump.
     */
    function tribe_get_ical_link()
    {
        $output = tribe('tec.iCal')->get_ical_link();
        /**
         * Filters the "Export Events" iCal link.
         *
         * Please note that tribe-events.js dynamically sets the iCal link in most contexts. To
         * override this behavior so that a custom link from the tribe_get_ical_link filter is the
         * one that's always used, please also use the tribe_events_force_filtered_ical_link filter.
         *
         * @see tribe_events_force_filtered_ical_link
         * @param string $output The "Export Events" iCal link URL.
         */
        return apply_filters('tribe_get_ical_link', $output);
    }
}

if (!function_exists('tribe_get_gcal_link')) {
    /**
     * Google Calendar Link
     *
     * Returns an "Add to Google Calendar" link for a single event. Must be used in the loop.
     *
     * @param int $postId (optional)
     *
     * @return string URL for google calendar.
     */
    function tribe_get_gcal_link($postId = null)
    {
        $postId = Tribe__Events__Main::postIdHelper($postId);
        $output = Tribe__Events__Main::instance()->googleCalendarLink($postId);
        /**
         * Filters the Google Calendar gcal link
         *
         * @param string $output Gcal link
         * @param int $postId WP Post ID of an event
         */
        return apply_filters('tribe_get_gcal_link', $output, $postId);
    }
}

