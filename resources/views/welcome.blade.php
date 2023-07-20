<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusher Web Notifications</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-notifications.min.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Demo Pusher Web App</a>
        </div>

        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown dropdown-notifications">
              <a href="#notifications-panel" class="dropdown-toggle" data-toggle="dropdown">
                <i data-count="0" class="glyphicon glyphicon-bell notification-icon"></i>
              </a>

              <div class="dropdown-container">
                <div class="dropdown-toolbar">
                  <div class="dropdown-toolbar-actions">
                    <a href="#">Mark all as read</a>
                  </div>
                  <h3 class="dropdown-toolbar-title">Notifications (<span class="notif-count">0</span>)</h3>
                </div>
                <ul class="dropdown-menu">
                </ul>
                <div class="dropdown-footer text-center">
                  <a href="#">View All</a>
                </div>
              </div>
            </li>
            <!-- <li><a href="#">Timeline</a></li>
            <li><a href="#">Friends</a></li> -->
          </ul>
        </div>
      </div>
    </nav>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="//js.pusher.com/3.1/pusher.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script type="text/javascript">
      var notificationsWrapper   = $('.dropdown-notifications');
      var notificationsToggle    = notificationsWrapper.find('a[data-toggle]');
      var notificationsCountElem = notificationsToggle.find('i[data-count]');
      var notificationsCount     = parseInt(notificationsCountElem.data('count'));
      var notifications          = notificationsWrapper.find('ul.dropdown-menu');

      if (notificationsCount <= 0) {
        notificationsWrapper.hide();
      }

      // Enable pusher logging - don't include this in production
       Pusher.logToConsole = true;

      var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
          cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
      });

      // Subscribe to the channel we specified in our Laravel Event
      var channel = pusher.subscribe('status-liked');

      // Bind a function to a Event (the full Laravel class)
      channel.bind('App\\Events\\StatusLiked', function(data) {
        var existingNotifications = notifications.html();
        var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
        var newNotificationHtml = `
          <li class="notification active">
              <div class="media">
                <div class="media-left">
                  <div class="media-object">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAWlBMVEXY2Nh0eoDb29txd31udHttc3re3t3W1tZ1e4HJysvP0NDExceEiY6jpqmxs7WLj5S7vb+rrbB+g4mSlpqgo6aZnaG8vsB7gIaoqq2vsbSCh4yVmZ2IjZHGx8lavitvAAAGo0lEQVR4nO2d53KjMBCAYVUAUU03mPd/zRO2k0tyPhuxYFYZfb9SZjJ82UVlVex5DofD4XA4HA6Hw+FwOBwOh8PhcDgcDoc9AEhPRVM8ReroR9kDkEHbX8oqPcfRbzQEKHIu8syTGrhx9DNtChSD6E6RKk5pU1XV2NRpVsQqCI5+sI2AoBJ+leah4Iwxv6zq/nQu4mg2DH6FpRqYH3ba7UrYhWwWDcs8LZT8Fdla3t1+whjnXR17tjvKhj8WvGuKLo3sDmT0nwh+jWVVWOwo65eG2pEnsbTSESCIyteC12RNIvsUQZ1zXywSvMYxtazNAa/3+YIM/QsvY5sUIQqftqGPw3iWRz/3YiC+tF5jFMIZUSlbwhhkALlxEHUYw1NgiSN4F+MIXuFdb0eLA+OKCN7jyOqIviQUi7uJR468yibikw4IEYKaoS480iM5yFbn6D2Igpd1QbjcAcO6ZuZOmJ2zdorjSZFN1dcTiuchbKnPjOGMTNILbb+5q8DF0GfR0QqvSLCGPfEgBh1OUKfp0QovUFhDn5FtRG8oZH+vJxkt7TTFG/ITcUN8lja0DdEtjc9y4obLCmzPGGgbegPasCNuiO3x9eCbtiGsrGB8gdE2lPlvN0SPvOln6ZLlmOcQb2mgxxqyhLghskyjDSvihi3asCZuGKOzlPjI21NIQZ8XxA097PRJ0K2U3pDYoXdHfI6P7vKpT5604QlpmJI3jDFLT7qhiSXt9W4AiewuslNCuSgMcZoiZxecccJFYZ2ijOGnT4T7fDhhx2zXKBLu8/Hj7iuU+/wI15DeGCg3poAvRBGfXGAXSGcE7T1u+HUL8pWoCj0/HIkbFtg05RltQy/Apimn3FfMSGyaluTnh9g9UeTnh96E6/SpL5BqAqQh+RmwB8gZMPWmVBvi1rk57RHNDOBmwPR3fWHXZsgXE7GlKJZTnjp9gKkJU67RfLJ6BhUOzOf0X8OZRKx6FYcgYY0NSao7/XOxQpCN0rPmnB6sqWZQ37L3HUjNDYlXL36wZik4pN8TfsN47EZ9h8JPzPfVWDDk/o55bZh69eInpqvdtiWp+SE9yssx/8PsiFdpn6DZ7ijqZxAeAv3yPBX060+PWF5YpLzq+xS59Fh+aFtP8cHiRQwLShf/YWm3b6+h+vUxVEtjaOt7uHxwakd15l9kvDBLLSh0PwTqxaOa0MJRqWe0HMyoH1h7iNFaqbAxiGb1NgvnFtKs9M3PtilCZliosa3XN7+Hx7YgrjjxXB79zEaA+XVmdpUTZb9qgc2W28w8MBjMfIUNkQ3XYAJ47bByiZTx5DQFpO/BAlDFGK5bH705MsESstfvglRZHppdevnYUodS0cvX+WZywzs9n0hylrekHAGiejO9u6ToMjJtKwRFvq3eDd6lJG78hKAv9/CbYX5zeAcCqsEfdHrmyPP4wN5Ddw7NXuH76yiS4qDeA+Q07hq/v47lEb0HyDh/i98V7o/Tex3BK1Zu7VoLE0P2tmTVr19fvtfv6sjDt3zSAMigqPxNjhmukGSXbN83EgDacePBi6Ej96v9buHVY5eRvT87/5Xk1S6zDwjSDSYO26D7j3TaOJAAmflt+XvCWL7pB2PAtHbevh/zh39spQheTyU/v8HEuM0EC6ILuQDeYeUW8ytofYoBvME2OK6Iuyd/dzbY04g8qbU7+BVyRTdFr6AXA/AXsO0Mek8c/vj5zqCvOKdviD1WSz9L0TFsibc0eEP0RYg74wx/gSG6pSFviL2SiL4hdmBK3hB9cRZ9w9AZOkNneDjO8CXB0QYvwF8sRb0StcGFNtB2dBOVlVtcNAFB/b71bCMYbzbaVfSOPRfmMFFN263N7L1vxhzGq43XvCE4DWQCyXhXq+2vsgGvHSkskjLOqmKnXX0wb9ITh0oyLoaz2nNLBsgg224jqbEee8vmWpCqqDr+7paH8TDfea/JV0kI2jThbwslY6Jril2T85HkvPsk3N+SzXui++iYLfwAMsqqku2Wsfovd/kphkO30ep/bZQ1yeaWOnS8HLMpIPEhz/oZVFEnfKOORMsJdqkLFVCQ+0S/l16cNYOPiub80oVaTg/ISNl9oJ9KBloz6XQDZObJ5qwMhyrVciTy8gnzf19FRTpetOdr0aua8JNRuymPutwX5iQLVJzVVRIKcVNln7rXL/UPhQgvTZ+1UeDRzMqXzPGUEtRUZGndjFWeDKVmuORNfcqKWM2/tdTtO3BHfnD//ujncjgcDofD4XA4HA6Hw+FwOBwOh8NBij9AwGWhlgF+qAAAAABJRU5ErkJggg==" class="img-circle" alt="50x50" style="width: 50px; height: 50px;">
                  </div>
                </div>
                <div class="media-body">
                  <strong class="notification-title">`+data.message+`</strong>
                  <!--p class="notification-desc">Extra description can go here</p-->
                  <div class="notification-meta">
                    <small class="timestamp">about a minute ago</small>
                  </div>
                </div>
              </div>
          </li>
        `;
        notifications.html(newNotificationHtml + existingNotifications);

        notificationsCount += 1;
        notificationsCountElem.attr('data-count', notificationsCount);
        notificationsWrapper.find('.notif-count').text(notificationsCount);
        notificationsWrapper.show();
      });
    </script>
  </body>
</html>