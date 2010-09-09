<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Untitled Document</title>
        <script runat="server" djConfig="parseOnLoad:true, isDebug:false" type="text/javascript" src="http://lo/js/lib/dojo/latest/dojo/dojo.js">
        </script>
        <script runat="server" src="http://jaxer/zir/ng/leecher/rules/<?php echo $_GET['rule_name'];?>.js">
        </script>
        <script runat="server" src="http://jaxer/zir/ng/leecher/leech.js">
        </script>
    </head>
    <body>
        <div id="console">
        </div>
        <script runat="server">
            var post = {
                url: "<?php echo $_GET['url'];?>",
                channel: "<?php echo $_GET['channel'];?>",
                type: "<?php echo $_GET['type'];?>",
                rule_name: "<?php echo $_GET['rule_name'];?>"
            }
            var rule = rules[post.channel];
            
            var post_url = "http://lo/zir/ng/leecher/backend/save.php";
            var lee = leech.init(rule);
            
            if (post.type == 'list') {
                var links = lee.parseListPage(200);
                post.links = dojo.toJson(links);
                lee.post(post_url, post);
            } else if (post.type == 'content') {
                var data = lee.parseContentPage(post.url);
                if (data != null) {
                    /*
                     data.cid = lee.options.type.cid;
                     data.mid = lee.options.type.mid;
                     data.digest = 3;
                     data.imagetolocal = 1;
                     data.selectimage = 1;
                     data.autofpage = 1;
                     */
                    post.title = data.title;
                    post.content = data.content;
					lee._log(data.content);
                    lee.post(post_url, post);
                    //lee.post('http://admintools.navgame.com/glee442460deef2b9c340f73/add.php', data);
                }
            } else if (post.type == 'paging') {
                var links = lee.parsePaginator(post.url);
                if (links) {
                    post.links = dojo.toJson(links);
                    lee.post(post_url, post);
                }
            }
        </script>
    </body>
</html>
