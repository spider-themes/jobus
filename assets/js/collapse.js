
jQuery(document).ready(function ($) {
  // first collapse show on page load

  var $firstTrigger = $('[data-jbs-toggle="collapse"]').first();
  var $firstTarget = $($firstTrigger.attr("href"));
  $firstTarget.addClass("jbs-show").show();
  $firstTrigger.attr("aria-expanded", "true").removeClass("jbs-collapsed");

  // Collapse toggle

  $('[data-jbs-toggle="collapse"]').on("click", function (e) {
    e.preventDefault();

    var $this = $(this);
    var $target = $($this.attr("href"));
    var isExpanded = $this.attr("aria-expanded") === "true";

    if (isExpanded) {
      $target.slideUp(300, function () {
        $target.removeClass("jbs-show");
      });

      $this.attr("aria-expanded", "false");
      $this.addClass("jbs-collapsed");
    } else {
      $target.hide().addClass("jbs-show").slideDown(300);
      $this.attr("aria-expanded", "true");
      $this.removeClass("jbs-collapsed");
    }
  });

  // Open offcanvas
  $(document).on("click", '[data-jbs-toggle="jbs-offcanvas"]', function (e) {
    e.preventDefault();
    var target = $(this).data("jbs-target");
    $(target).addClass("show");
    $(".jbs-offcanvas-backdrop").addClass("show");
  });

  // Close button
  $(document).on("click", ".jbs-offcanvas-close", function () {
    $(this).closest(".jbs-offcanvas").removeClass("show");
    $(".jbs-offcanvas-backdrop").removeClass("show");
  });

  // Click on backdrop
  $(document).on("click", ".jbs-offcanvas-backdrop", function () {
    $(".jbs-offcanvas.show").removeClass("show");
    $(this).removeClass("show");
  });

  // modal close on esc key press
  $(".jbs-open-modal").on("click", function (e) {
    e.preventDefault();
    var target = $(this).data("target");
    $(target).fadeIn(300).addClass("show");
  });

  // -----------------------------
  // Close Modal
  // -----------------------------
  $(".jbs-btn-close").on("click", function () {
    var modal = $(this).closest(".jbs-modal");
    modal.fadeOut(300).removeClass("show"); // fadeOut + remove show
  });

  // -----------------------------
  // Click Outside Modal Content
  // -----------------------------
  $(".jbs-modal").on("click", function (e) {
    if ($(e.target).is(".jbs-modal")) {
      // only if click on backdrop
      $(this).fadeOut(300).removeClass("show");
    }
  });

  $(".filter-header").on("click", function () {
    $(this).toggleClass("jbs-collapsed");
    $(".jbs-collapse").slideToggle(300);
  });

  // Tabs

  $("[data-jbs-tab-target]").on("click", function () {
    let target = $(this).data("jbs-tab-target");

    // Active class remove from all tab button
    $("[data-jbs-tab-target]").removeClass("active");

    // Content fade out
    $(".jbs-tab-pane").removeClass("active show").stop(true, true).fadeOut(200);

    // Current tab active
    $(this).addClass("active");

    // Target content fade in
    $(target).stop(true, true).fadeIn(300).addClass("active show");
  });
});

