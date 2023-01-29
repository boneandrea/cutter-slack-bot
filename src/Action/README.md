# What

To add an action:

## Create Action

Create a new class in Actions/NewAction.php

```
<?php

declare(strict_types=1);

namespace App\Action;

class NewAction implements IPerform
{
    public function test(string $text)
    {
        // condition 
        return preg_match("/神/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        // action
        return $slack->send_image(
            message: "そうでしゅねぇ〜",
            channels: "#general",
            thread_ts: $thread_ts,
            alt_text: "唯一神.jpg",
            image:"god.jpg"
        );
    }
}
```

use in CutterBot.php:
```
public function initResolver(): Resolver
{
        $resolver=new Resolver();
        $resolver->add(new Kurosawa());
        $resolver->add(new NgWord());
        $resolver->add(new Munou());
        $resolver->add(new God());
        $resolver->add(new Shiga());

        $resolver->add(new NewAction());
```
