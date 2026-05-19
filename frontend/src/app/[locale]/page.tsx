import { Container, SectionWrapper, Button, Card } from "@/components/ui";

export default function HomePage() {
  return (
    <>
      <SectionWrapper background="white" className="relative overflow-hidden bg-[radial-gradient(circle_at_20%_10%,rgba(91,124,250,0.18),transparent_32%),linear-gradient(180deg,#fbfcff,#eef3ff)]">
        <div className="absolute inset-0 myliba-grid opacity-50" />
        <Container className="relative">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-[72vh]">
            <div className="reveal">
              <div className="mb-6 inline-flex rounded-full border border-primary/20 bg-white/70 px-4 py-2 text-sm font-bold text-primary shadow-sm backdrop-blur">
                OKR and Culture Software + Academy
              </div>
              <h1 className="text-5xl md:text-6xl lg:text-7xl font-black text-foreground leading-[0.95] tracking-tight">
                We Create<br />
                a High <span className="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent italic">Performance</span><br />
                Culture!
              </h1>
              <p className="mt-6 text-lg md:text-xl leading-8 text-text-secondary max-w-xl">
                Turn Strategy into Action: Make the contribution transparent by turning
                strategy into goals and goals into actions. Focus on &quot;what matters most&quot;
                by developing a motivated, committed and competent team.
              </p>
              <div className="mt-5 flex flex-wrap gap-3">
                <p className="rounded-full border border-primary/15 bg-white/70 px-4 py-2 text-primary font-bold">OKR and Culture Software</p>
                <p className="rounded-full border border-accent/15 bg-white/70 px-4 py-2 text-accent font-bold">OKR and Culture Academy</p>
              </div>
              <div className="mt-8">
                <Button variant="primary" size="lg" href="/en/contact">
                  Get a Quote
                </Button>
              </div>
            </div>
            <div className="hidden lg:flex justify-center">
              <div className="relative w-full max-w-lg rounded-[2rem] border border-white/70 bg-[#0b1020] p-4 shadow-[0_40px_120px_rgba(13,24,45,0.25)]">
                <div className="rounded-[1.5rem] bg-white/[0.06] p-5 text-white">
                  <div className="mb-5 flex items-center justify-between">
                    <span className="text-xs font-bold uppercase tracking-[0.22em] text-white/40">Performance cockpit</span>
                    <span className="rounded-full bg-success/20 px-3 py-1 text-xs text-emerald-100">Live</span>
                  </div>
                  <div className="grid grid-cols-3 gap-3">
                    {["Goals", "Actions", "Culture"].map((item, i) => (
                      <div key={item} className="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                        <div className="text-2xl font-black">{[94, 76, 87][i]}%</div>
                        <div className="mt-2 text-xs text-white/55">{item}</div>
                      </div>
                    ))}
                  </div>
                  <div className="mt-5 space-y-3">
                    {["Strategy alignment", "Team contribution", "Feedback rhythm"].map((item, i) => (
                      <div key={item} className="rounded-2xl bg-white/[0.07] p-4">
                        <div className="flex justify-between text-sm">
                          <span>{item}</span>
                          <span className="text-cyan-200">{82 - i * 8}%</span>
                        </div>
                        <div className="mt-3 h-2 rounded-full bg-white/10">
                          <div className="h-full rounded-full bg-gradient-to-r from-cyan-300 to-primary" style={{ width: `${82 - i * 8}%` }} />
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
                <div className="glass-panel absolute -right-8 bottom-8 w-48 rounded-3xl p-4">
                  <p className="text-xs font-black uppercase tracking-wider text-primary">Action intelligence</p>
                  <p className="mt-2 text-2xl font-black">+31%</p>
                  <p className="text-xs text-text-secondary">CFR rhythm improvement</p>
                </div>
              </div>
            </div>
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <div className="flex flex-wrap items-center justify-center gap-4 opacity-80">
            {Array.from({ length: 6 }).map((_, i) => (
              <div key={i} className="h-14 w-36 rounded-2xl border border-border bg-white shadow-sm flex items-center justify-center text-xs font-black text-text-secondary">
                Client Logo
              </div>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="white">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-4 tracking-tight">
            For Employees Who Are Willing and Committed to the Goal;
          </h2>
          <p className="text-center text-text-secondary mb-12 max-w-2xl mx-auto text-lg leading-8">
            With Myliba&apos;s Three Superpowers, Performance And Potential Are Constantly Increased.
          </p>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { title: "Declare your goal!", text: "Declare your goal by knowing what you are working for and how you will contribute! Build your team in seconds." },
              { title: "Clarify contribution and involvement!", text: "Track who wants to achieve what and how much they contribute. See and remove risks and obstacles instantly." },
              { title: "Celebrate results together!", text: "See and appreciate the results achieved on time and celebrate together with all contributing teams!" },
            ].map((item, index) => (
              <Card key={item.title} hoverable className="text-center min-h-72">
                <div className="w-16 h-16 bg-gradient-to-br from-primary/15 to-accent/15 rounded-2xl mx-auto mb-6 flex items-center justify-center text-xl font-black text-primary">
                  {index + 1}
                </div>
                <h3 className="text-xl font-black mb-3">{item.title}</h3>
                <p className="text-text-secondary leading-7">{item.text}</p>
              </Card>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">Videos</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { title: "Watch alignment and progress from strategy to action on one screen!", url: "https://www.youtube.com/watch?v=d1eO_3qHXrQ" },
              { title: "Unlock potential and manage talent!", url: "https://www.youtube.com/watch?v=jqnLdpfhKUI" },
              { title: "See and appreciate results instantly!", url: "https://www.youtube.com/watch?v=73_Vu3wjEgA" },
            ].map((video) => (
              <a key={video.url} href={video.url} target="_blank" rel="noopener noreferrer" className="group block">
                <Card hoverable>
                  <div className="aspect-video rounded-2xl mb-4 flex items-center justify-center bg-[radial-gradient(circle_at_30%_20%,rgba(91,124,250,0.24),transparent_35%),linear-gradient(135deg,#101523,#25314d)] transition-transform group-hover:scale-[1.01]">
                    <svg className="w-14 h-14 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8 5v14l11-7z" />
                    </svg>
                  </div>
                  <p className="font-bold text-sm leading-6">{video.title}</p>
                </Card>
              </a>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="white">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">
            Game-Changing Next-Generation Performance
          </h2>
          <div className="mt-12 space-y-12">
            {[
              { title: "Don't stress over points!", text: "Stop performance interviews that require hours of preparation and create workload!", highlight: "Myliba makes you look forward to the 1-on-1 interview." },
              { title: "Don't give directives with hierarchy!", text: "Don't set goals with a chain of command. Don't try to fit goals by department or status!", highlight: "Myliba enables you to declare high goals and create a goal-oriented organization." },
              { title: "Don't lag behind the competition!", text: "Don't get stuck in KPIs! Do not leave your future strategies that you worked for months hanging in the air!", highlight: "Myliba brings strategy down to action." },
            ].map((feature, i) => (
              <div key={feature.title} className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center rounded-[2rem] border border-border bg-white p-6 shadow-sm">
                <div className={i % 2 === 1 ? "md:order-2" : ""}>
                  <h3 className="text-2xl md:text-3xl font-black mb-4">{feature.title}</h3>
                  <p className="text-text-secondary mb-4 leading-7">{feature.text}</p>
                  <p className="text-lg font-bold italic text-primary">{feature.highlight}</p>
                </div>
                <div className={`aspect-[4/3] rounded-[1.5rem] bg-[linear-gradient(135deg,#eef3ff,#ffffff)] border border-border flex items-center justify-center ${i % 2 === 1 ? "md:order-1" : ""}`}>
                  <div className="w-3/4 space-y-4">
                    <div className="h-4 rounded-full bg-primary/20" />
                    <div className="h-4 w-4/5 rounded-full bg-accent/20" />
                    <div className="grid grid-cols-3 gap-3 pt-4">
                      <div className="h-20 rounded-2xl bg-white shadow-sm" />
                      <div className="h-20 rounded-2xl bg-white shadow-sm" />
                      <div className="h-20 rounded-2xl bg-white shadow-sm" />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="surface">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">
            Who Uses <span className="text-primary">MYLIBA</span>
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              "Senior management and strategy professionals who want to turn strategy into action",
              "Institutions that want to establish a new generation performance improvement system",
              "HR professionals who want to develop a high performance culture",
            ].map((text) => (
              <Card key={text} className="text-center min-h-56">
                <div className="w-20 h-20 bg-gradient-to-br from-primary/15 to-accent/15 rounded-3xl mx-auto mb-5" />
                <p className="text-text-secondary leading-7">{text}</p>
              </Card>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="white">
        <Container>
          <h2 className="text-3xl md:text-5xl font-black text-center mb-12 tracking-tight">Myliba Team</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { name: "Dilek Mete", role: "Managing Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach" },
              { name: "Aysel Eker", role: "Partner, Cultural Transformation Consultant, Executive Coach, PCC, OKR Coach" },
              { name: "Huri Sankur", role: "People and Culture Advisor, Academy Coordinator, OKR Coach" },
            ].map((member) => (
              <Card key={member.name} className="text-center min-h-72">
                <div className="w-28 h-28 bg-gradient-to-br from-[#101523] to-primary rounded-[2rem] mx-auto mb-5 flex items-center justify-center text-2xl font-black text-white">
                  {member.name.split(" ").map((part) => part[0]).join("")}
                </div>
                <h3 className="text-xl font-black">{member.name}</h3>
                <p className="text-sm text-text-secondary mt-2 leading-6">{member.role}</p>
              </Card>
            ))}
          </div>
        </Container>
      </SectionWrapper>

      <SectionWrapper background="primary">
        <Container className="text-center">
          <h2 className="text-3xl md:text-5xl font-black mb-4 tracking-tight">
            How About Discovering Our Products?
          </h2>
          <p className="text-white/80 mb-8 max-w-xl mx-auto text-lg">
            We Are Ready To Offer You a Wonderful Experience!
          </p>
          <Button variant="accent" size="lg" href="/en/contact">
            GET AN OFFER!
          </Button>
        </Container>
      </SectionWrapper>
    </>
  );
}
