# What

slackボット


チャンネルに常駐し、単語に反応してメッセージをポストする

# Config

slack appを作る  

.env

```
CLIENT=000000000000.0000000000000
SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxx
BOT_SELF_USERID=zzzzzzzzzzz
```

SlackBotはLINEbotと違い、自分の発言も飛んでくるので`BOT_SELF_USERID`で避けないとループする。


## NGワード
`ROOT/ng_words.txt`に追記する
