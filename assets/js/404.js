gsap.registerPlugin(MorphSVGPlugin);

let yetiTL, chatterTL,
  furLightColor = "#FFF",
  furDarkColor = "#67b1e0",
  skinLightColor = "#ddf1fa",
  skinDarkColor = "#88c9f2",
  lettersSideLight = "#3A7199",
  lettersSideDark = "#051d2c",
  lettersFrontLight = "#67B1E0",
  lettersFrontDark = "#051d2c",
  lettersStrokeLight = "#265D85",
  lettersStrokeDark = "#031219",
  mouthShape1 = "M149 115.7c-4.6 3.7-6.6 9.8-5 15.6.1.5.3 1.1.5 1.6.6 1.5 2.4 2.3 3.9 1.7l11.2-4.4 11.2-4.4c1.5-.6 2.3-2.4 1.7-3.9-.2-.5-.4-1-.7-1.5-2.8-5.2-8.4-8.3-14.1-7.9-3.7.2-5.9 1.1-8.7 3.2z",
  mouthShape2 = "M161.2 118.9c0 2.2-1.8 4-4 4s-4-1.8-4-4c0-1 .4-2 1.1-2.7.7-.8 1.8-1.3 2.9-1.3 2.2 0 4 1.7 4 4z",
  mouthShape3 = "M150.2 118.3c-4.6 3.7-7.5 6.4-6.3 12.3.1.5.1.6.3 1.1.6 1.5 2.4 2.3 3.9 1.7 0 0 7.9-4.3 10.7-5.5s11.6-3.3 11.6-3.3c1.5-.6 2.3-2.4 1.7-3.9-.2-.5-.2-.6-.4-1.1-2.8-5.2-7.1-4.9-12.9-4.6-3.7.4-6.3 1.5-8.6 3.3z",
  mouthShape4 = "M149.2 116.7c-4.6 3.7-6.7 8.8-5.2 14.6.1.3.1.5.2.8.6 1.5 2.4 2.3 3.9 1.7l11.2-4.4 11.2-4.4c1.5-.6 2.3-2.4 1.7-3.9-.1-.3-.2-.5-.4-.7-2.8-5.2-8.2-7.2-14-6.9-3.6.2-5.9 1.1-8.6 3.2z";

chatterTL = gsap.timeline({ paused: true, repeat: -1, yoyo: true });

chatterTL
  .to(['#mouthBG', '#mouthPath', '#mouthOutline'], { duration: 0.1, morphSVG: mouthShape4 }, 0)
  .to('#chin', { duration: 0.1, y: 1.5 }, 0);

yetiTL = gsap.timeline({ repeat: -1 });

yetiTL
  .add(() => chatterTL.play(), 0)

  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 2.5)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 2.575)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 2.65)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 2.725)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 2.8)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 2.875)

  .add(goLight, 3.2)
  .add(goDark, 3.3)
  .add(goLight, 3.4)

  .add(() => {
    chatterTL.pause();
    gsap.to(['#mouthBG', '#mouthPath', '#mouthOutline'], { duration: 0.1, morphSVG: mouthShape1 });
  }, 3.2)

  .to(['#mouthBG', '#mouthPath', '#mouthOutline'], { duration: 0.25, morphSVG: mouthShape2 }, 5)
  .to('#tooth1', { duration: 0.1, y: -5 }, 5)
  .to('#armR', { duration: 0.5, x: 10, y: 30, rotation: 10, transformOrigin: "bottom center", ease: "power1.out" }, 4)
  .to(['#eyeL', '#eyeR'], { duration: 0.25, scaleX: 1.4, scaleY: 1.4, transformOrigin: "center center" }, 5)

  .add(goDark, 8)
  .add(goLight, 8.1)
  .add(goDark, 8.3)
  .add(goLight, 8.4)
  .add(goDark, 8.6)

  .to(['#mouthBG', '#mouthPath', '#mouthOutline'], { duration: 0.25, morphSVG: mouthShape1 }, 9)
  .to('#tooth1', { duration: 0.1, y: 0 }, 9)
  .to('#armR', { duration: 0.5, x: 0, y: 0, rotation: 0, transformOrigin: "bottom center", ease: "power1.out" }, 9)
  .to(['#eyeL', '#eyeR'], { duration: 0.25, scaleX: 1, scaleY: 1, transformOrigin: "center center" }, 9)
  .add(() => chatterTL.play(), 9.25)

  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 11.5)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 11.575)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 11.65)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 11.725)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 7 }, 11.8)
  .to(['#armL', '#flashlightFront'], { duration: 0.075, x: 0 }, 11.875);

function goDark() {
  gsap.set('#light', { visibility: "hidden" });
  gsap.set('.lettersSide', { fill: lettersSideDark, stroke: lettersStrokeDark });
  gsap.set('.lettersFront', { fill: lettersFrontDark, stroke: lettersStrokeDark });
  gsap.set('#lettersShadow', { opacity: 0.05 });
  gsap.set('.hlFur', { fill: furDarkColor });
  gsap.set('.hlSkin', { fill: skinDarkColor });
}

function goLight() {
  gsap.set('#light', { visibility: "visible" });
  gsap.set('.lettersSide', { fill: lettersSideLight, stroke: lettersStrokeLight });
  gsap.set('.lettersFront', { fill: lettersFrontLight, stroke: lettersStrokeLight });
  gsap.set('#lettersShadow', { opacity: 0.2 });
  gsap.set('.hlFur', { fill: furLightColor });
  gsap.set('.hlSkin', { fill: skinLightColor });
}

goDark();