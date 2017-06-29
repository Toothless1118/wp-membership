<!-- Twitter -->
<script>
    /* We want to reload the button after liveeditor element gets edited */
    if (typeof twttr === "object"
        && typeof twttr.widgets === "object"
        && typeof twttr.widgets.load === "function") {
        try {
            twttr.widgets.load();
        } catch (e) {}
    }
</script>
