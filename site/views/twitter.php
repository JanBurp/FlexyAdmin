<div id="twitter">
  <a id="twitter_head" href="http://www.twitter.com/<?=$user?>" target="_blank"><img src="site/assets/img/twitter.gif" width="188" height="50" alt="Twitter" /></a>
  <script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
  <script>
    new TWTR.Widget({
      version: 2,
      // type: 'profile',
      // type: 'search',
      search: 'from:<?=$user?>',
      // interval: 500,
      title: 'Twitter',
      subject: "Twitter",
      width: 188,
      height: 353,
      theme: {
        shell: {
          background: '#A9A183',
          color: '#000000'
        },
        tweets: {
          background: '#A9A183',
          color: '#000',
          links: '#983588'
        }
      },
      features: {
        scrollbar: false,
        loop: true,
        live: true,
        behavior: 'default'
      }
    }).render().start()
  </script>
  </div>
